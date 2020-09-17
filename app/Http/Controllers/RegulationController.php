<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegulationStoreFilesRequest;
use App\Http\Requests\RegulationStoreRequest;
use App\Http\Resources\RegulationListDataResource;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Regulation;
use App\Models\RegulationFile;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

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
    public function index()
    {
        $model = Regulation::select(
            'regulation.*',
            'institution.name as institution_name'
        )
        ->leftJoin('institution','institution.id', '=', 'institution_id')
        ->get();

        return DataTables::of(RegulationListDataResource::collection($model))->toJson();
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
        $regulation->publish_date   = $request->input('publish_date');
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
