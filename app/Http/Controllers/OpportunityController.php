<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpportunityStoreFilesRequest;
use App\Http\Requests\OpportunityStoreRequest;
use App\Http\Resources\OpportunityListDataResource;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Opportunity;
use App\Models\OpportunityFile;
use App\Models\OpportunityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OpportunityController extends Controller
{
    private function getInstitutionId()
    {
        $user = auth()->user();

        $typeName = [
            'institution'   => 0,
            'researcher'    => 1,
        ];

        if ($user->type == $typeName['institution']) {
            $institution = Institution::find($user->owner_id);

            $institution_id = $institution->id;
        } else if ($user->type == $typeName['researcher']) {
            $member = Member::findOrFail($user->owner_id);

            $institution_id = $member->department->institution->id;
        }

        return $institution_id;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rules['grid'] = 'required|in:default,datatable';
        $rules['draw'] = 'required_if:grid,datatable|integer';
        $rules['columns'] = 'required_if:grid,datatable';
        $rules['start'] = 'required|integer|min:0';
        $rules['length'] = 'required|integer|min:1|max:100';
        $rules['options_active_only'] = 'boolean';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseStatus = 'Missing Param';
            $this->responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData['error_log'] = $validator->errors();
        } else {
            $this->responseCode = 200;
            $grid = ($request->input('grid') == 'datatable') ? 'datatable' : 'default';

            if ($grid == 'datatable') {
                $numbcol = $request->get('order');
                $columns = $request->get('columns');

                $echo = $request->get('draw');


                $sort = $numbcol[0]['dir'];
                $field = $columns[$numbcol[0]['column']]['data'];
            } else {
                $sort = $request->input('order_method');
                $field = $request->input('order_column');
            }

            $start = $request->get('start');
            $perpage = $request->get('length');

            $search = $request->get('search_value');
            $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
            $search = preg_replace($pattern, '', $search);

            $options = ['grid' => $grid, 'active_only' => $request->get('options_active_only'), 'profile' => $request->get('profile')];

            $result = Opportunity::listData($start, $perpage, $search, false, $sort, $field, $options);
            $total = Opportunity::listData($start, $perpage, $search, true, $sort, $field, $options);

            if ($grid == 'datatable') {
                $this->responseData['sEcho'] = $echo;
                $this->responseData["iTotalRecords"] = $total;
                $this->responseData["iTotalDisplayRecords"] = $total;
                $this->responseData["aaData"] = OpportunityListDataResource::collection($result);
                return response()->json($this->responseData, $this->responseCode);
            } else {
                $this->responseData['opportunity'] = OpportunityListDataResource::collection($result);
                $pagination['row'] = count($result);
                $pagination['rowStart'] = ((count($result) > 0) ? ($start + 1) : 0);
                $pagination['rowEnd'] = ($start + count($result));
                $this->responseData['meta']['start'] = $start;
                $this->responseData['meta']['perpage'] = $perpage;
                $this->responseData['meta']['search'] = $search;
                $this->responseData['meta']['total'] = $total;
                $this->responseData['meta']['pagination'] = $pagination;
            }
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OpportunityStoreRequest $request, Opportunity $opportunity)
    {
        $request->validated();

        $institutionId = $this->getInstitutionId();

        $target = $request->input('target');
        $institutions = $request->input('institutions');

        $opportunity->name                  = $request->input('name');
        $opportunity->opportunity_type_id   = $request->input('opportunity_type_id');
        $opportunity->desc                  = $request->input('desc');
        $opportunity->total_funding         = $request->input('total_funding');
        $opportunity->contact_person        = $request->input('contact_person');
        $opportunity->start_date            = date('Y-m-d', strtotime($request->input('start_date')));
        $opportunity->end_date              = date('Y-m-d', strtotime($request->input('end_date')));
        $opportunity->target                = $target;
        $opportunity->keyword               = $request->input('keyword');
        $opportunity->institution_id        = $institutionId;
        $opportunity->save();

        // 0 = all institution,1 = my institution, 2 = selected institution
        if ($target == 0) {
            $institution = Institution::get()->pluck('id');
            $opportunity->institutionTarget()->sync($institution);
        } else if ($target == 1) {
            $opportunity->institutionTarget()->sync([$institutionId]);
        } else {
            if ($institutions != null) {
                $opportunity->institutionTarget()->sync($institutions);
            }
        }

        $this->responseCode = 200;
        $this->responseData = $opportunity->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * uploadFiles function handling upload file
     *
     * @param Type $var Description
     * @return type
     **/
    public function storeFiles(OpportunityStoreFilesRequest $request, Opportunity $opportunity)
    {
        $request->validated();

        //simpan foto
        $file = $request->file('file');
        if (!empty($file)) {
            $jumlahFile = count($file);
            for ($i = 0; $i < $jumlahFile; $i++) {
                if ($file[$i]->isValid()) {
                    $regulationFile = new OpportunityFile();

                    $changedName = time().random_int(100,999).$file[$i]->getClientOriginalName();
                    $is_image = false;
                    if(substr($file[$i]->getClientMimeType(), 0, 5) == 'image') {
                        $is_image = true;
                    }
                    $file[$i]->storeAs('opportunity/' . $opportunity->id, $changedName);

                    $arrayFoto = [
                        'opportunity_id'      => $opportunity->id,
                        'name'                => $file[$i]->getClientOriginalName(),
                        'path'                => $changedName,
                        'size'                => $file[$i]->getSize(),
                        'ext'                 => $file[$i]->getClientOriginalExtension(),
                        'is_image'            => $is_image,
                    ];

                    $regulationFile->create($arrayFoto);
                }
            }

            $file = OpportunityFile::where('opportunity_id', $opportunity->id)->get()->makeHidden([
                'id',
                'created_at',
                'created_by',
                'updated_at',
                'updated_by'
            ]);
        }

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';
        $this->responseData = $file;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * interest function used for intersting an opportunity
     *
     * @param Opportunity $opportunity Description
     * @return json
     **/
    public function interest(Opportunity $opportunity)
    {
        $user = auth()->user();

        $owner_id = $user->owner_id;

        $opportunity->interest()->sync([$owner_id]);

        $this->responseCode = 200;
        $this->responseMessage = 'Anda tertarik pada opportunity ini';

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function showFile(OpportunityFile $opportunityFile)
    {
        $path = storage_path('app/opportunity/'.$opportunityFile->opportunity_id.'/'.$opportunityFile->path);
        return response()->file($path);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Opportunity $opportunity)
    {
        $this->responseCode = 200;
        $this->responseData = $opportunity;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getTypeOpportunity()
    {
        $model = OpportunityType::all();

        $this->responseCode = 200;
        $this->responseData = $model;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getInstitution()
    {
        $model = Institution::all();

        $this->responseCode = 200;
        $this->responseData = $model;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Opportunity $opportunity)
    {
        $opportunity->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
