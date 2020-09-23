<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResearchGroupStoreRequest;
use App\Http\Resources\ResearchGroupListDataResource;
use App\Models\ResearchGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ResearchGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = ResearchGroup::get();

        return DataTables::of(ResearchGroupListDataResource::collection($model))->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ResearchGroupStoreRequest $request, ResearchGroup $researchGroup)
    {
        $request->validated();

        $researchGroup->name    = $request->input('name');
        $researchGroup->desc    = $request->input('desc');
        $researchGroup->topic   = $request->input('topic');
        $researchGroup->save();

        $this->responseCode     = 200;
        $this->responseMessage  = 'Data berhasil disimpan';
        $this->responseData     = $researchGroup->refresh();

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function join()
    {
        # code...
    }

    public function listOfMember(ResearchGroup $researchGroup)
    {
        $this->responseCode     = 200;
        $this->responseMessage  = 'Data berhasil disimpan';
        $this->responseData     = $researchGroup->load('memberGroup');

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function selectAsAdmin(ResearchGroup $researchGroup)
    {
        # code...
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ResearchGroup $researchGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(ResearchGroup $researchGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResearchGroup $researchGroup)
    {
        $researchGroup->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
