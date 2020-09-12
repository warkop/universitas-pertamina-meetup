<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegulationStoreFilesRequest;
use App\Http\Requests\RegulationStoreRequest;
use App\Http\Resources\RegulationListDataResource;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Regulation;
use App\Models\RegulationFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegulationController extends Controller
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
                $order = $request->input('order');

                $sort = $request->input('order_method');
                $field = $request->input('order_column');
            }

            $start = $request->get('start');
            $perpage = $request->get('length');

            $search = $request->get('search_value');
            $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
            $search = preg_replace($pattern, '', $search);

            $options = ['grid' => $grid, 'active_only' => $request->get('options_active_only')];

            $result = Regulation::listData($start, $perpage, $search, false, $sort, $field, $options);
            $total = Regulation::listData($start, $perpage, $search, true, $sort, $field, $options);

            if ($grid == 'datatable') {
                $this->responseData['sEcho'] = $echo;
                $this->responseData["iTotalRecords"] = $total;
                $this->responseData["iTotalDisplayRecords"] = $total;
                $this->responseData["aaData"] = RegulationListDataResource::collection($result);
                return response()->json($this->responseData, $this->responseCode);
            } else {
                $this->responseData['regulation'] = RegulationListDataResource::collection($result);
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
    public function store(RegulationStoreRequest $request, Regulation $regulation)
    {
        $request->validated();
        $institutionId = $this->getInstitutionId();

        $regulation->institution_id = $institutionId;
        $regulation->name           = $request->input('name');
        $regulation->code           = $request->input('code');
        $regulation->regulator      = $request->input('regulator');
        $regulation->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';
        $this->responseData = $regulation;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * getFile function summary
     *
     * Undocumented function long description
     *
     * @param string $fileName showfile
     * @return Response
     **/
    public function showFile(RegulationFile $regulationFile)
    {
        $path = storage_path('app/regulation/'.$regulationFile->regulation_id.'/'.$regulationFile->path);
        return response()->file($path);
    }

    /**
     * storeFiles function for storing file depend regulation id
     *
     * @param RegulationStoreFilesRequest $request Get request
     * @param Regulation $regulation Get model
     * @return json
     **/
    public function storeFiles(RegulationStoreFilesRequest $request, Regulation $regulation)
    {
        $request->validated();

        //simpan foto
        $file = $request->file('file');
        if (!empty($file)) {
            $jumlahFile = count($file);
            for ($i = 0; $i < $jumlahFile; $i++) {
                if ($file[$i]->isValid()) {
                    $regulationFile = new RegulationFile();

                    $changedName = time().random_int(100,999).$file[$i]->getClientOriginalName();
                    $is_image = false;
                    if(substr($file[$i]->getClientMimeType(), 0, 5) == 'image') {
                        $is_image = true;
                    }
                    $file[$i]->storeAs('regulation/' . $regulation->id, $changedName);

                    $arrayFoto = [
                        'regulation_id'       => $regulation->id,
                        'name'                => $file[$i]->getClientOriginalName(),
                        'path'                => $changedName,
                        'size'                => $file[$i]->getSize(),
                        'ext'                 => $file[$i]->getClientOriginalExtension(),
                        'is_image'            => $is_image,
                    ];

                    $regulationFile->create($arrayFoto);
                }
            }

            $file = RegulationFile::where('regulation_id', $regulation->id)->get()->makeHidden([
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Regulation $regulation)
    {
        $this->responseCode = 200;
        $this->responseData = $regulation;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getListInstitution()
    {
        $institution = Institution::all();

        $this->responseCode = 200;
        $this->responseData = $institution;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Regulation $regulation)
    {
        $regulation->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
