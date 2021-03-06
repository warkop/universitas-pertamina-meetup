<?php

namespace App\Http\Controllers;

use App\Helpers\HelperPublic;
use App\Http\Requests\ListDataOpportunityRequest;
use App\Http\Requests\OpportunityStoreFilesRequest;
use App\Http\Requests\OpportunityStoreRequest;
use App\Http\Resources\DetailOpportunityResource;
use App\Http\Resources\OpportunityFileResource;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Opportunity;
use App\Models\OpportunityFile;
use App\Models\OpportunityType;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;


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
    public function index(ListDataOpportunityRequest $request)
    {
        $request->validated();
        $user = auth()->user();
        $options = [
            'profile' => request()->profile
        ];
        if ($user->type == 2) {
            $options = [
                'profile' => null
            ];
        }

        if ($request->target == 1) {
            $options['institution'] = [$this->getInstitutionId()];
        } else if ($request->target == 2 && $request->institutions != null) {
            $options['institution'] = $request->institutions;
        } else {
            $options['institution'] = Institution::get()->pluck('id');
        }

        $model = Opportunity::listData($options);
        return DataTables::of($model)
        ->setTransformer(function($item){
            return [
                'id'                    => $item->id,
                'name'                  => $item->name,
                'desc'                  => $item->desc,
                'contact_person'        => $item->contact_person,
                'total_funding'         => HelperPublic::helpCurrency($item->total_funding, '', '.', false),
                'opportunity_type_name' => $item->opportunity_type_name??null,
                'institution_name'      => $item->institution_name??null,
                'institution_id'        => $item->institution_id??null,
                'institution_photo'     => $item->institution_path_photo??null,
                'start_date'            => $item->start_date,
                'end_date'              => Carbon::parse($item->end_date)->format('d F Y'),
                'deadline'              => Carbon::parse($item->deadline)->format('d F Y'),
                'created_at'            => date('d-m-Y H:i:s', strtotime($item->created_at)),
                'updated_at'            => date('d-m-Y H:i:s', strtotime($item->updated_at)),
            ];
        })
        ->filterColumn('updated_at', function($query, $keyword) {
            $keyword = date('d-m-Y', strtotime($keyword));
            $sql = "TO_CHAR(updated_at, 'dd-mm-yyyy') like ?";
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })
        ->filterColumn('end_date', function($query, $keyword) {
            $keyword = date('d-m-Y', strtotime($keyword));
            $sql = "TO_CHAR(end_date, 'dd-mm-yyyy') like ?";
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })
        ->filterColumn('opportunity_type_name', function($query, $keyword) {
            $sql = "opportunity_type.name like ?";
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })
        ->filterColumn('institution_name', function($query, $keyword) {
            $sql = "institution.name like ?";
            $query->whereRaw($sql, ["%{$keyword}%"]);
        })
        ->filterColumn('total_funding', function($query, $keyword) {
            $sql = "total_funding like ?";
            $query->whereRaw($sql, ["%{str_replace('.', '', $keyword)}%"]);
        })
        ->toJson();
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
        $opportunity->start_date            = date('Y-m-d H:i:s', strtotime($request->input('start_date')));
        $opportunity->end_date              = date('Y-m-d H:i:s', strtotime($request->input('end_date')));
        $opportunity->target                = $target;
        $opportunity->keyword               = $request->input('keyword');
        $opportunity->contact_person_email  = $request->input('contact_person_email');
        $opportunity->deadline              = date('Y-m-d H:i:s', strtotime($request->input('deadline')));
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
                $opportunity = Opportunity::find($opportunity->id);
                $opportunity->institutionTarget()->sync($institutions);
            }
        }

        $this->responseCode = 200;
        $this->responseData = $opportunity->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function listFile(Opportunity $opportunity)
    {
        $files = OpportunityFile::where('opportunity_id', $opportunity->id)->get();
        $this->responseCode = 200;
        $this->responseData = OpportunityFileResource::collection($files);

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

        $opportunity->interest()->syncWithoutDetaching([$owner_id]);

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
        $this->responseData = new DetailOpportunityResource($opportunity);

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

    public function destroyFile(OpportunityFile $opportunityFile)
    {

        if(File::exists(storage_path('app/opportunity/'.$opportunityFile->opportunity_id.'/'.$opportunityFile->path))){
            File::delete(storage_path('app/opportunity/'.$opportunityFile->opportunity_id.'/'.$opportunityFile->path));
        }
        $opportunityFile->forceDelete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
