<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicationTypeStoreRequest;
use App\Http\Resources\PublicationTypeListDataResource;
use App\Models\PublicationType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PublicationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = PublicationType::get();

        return DataTables::of(PublicationTypeListDataResource::collection($model))->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PublicationTypeStoreRequest $request, PublicationType $publicationType)
    {
        $request->validated();

        $publicationType->name           = $request->input('name');
        $publicationType->save();

        $this->responseCode     = 200;
        $this->responseMessage  = 'Data berhasil disimpan';
        $this->responseData     = $publicationType;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PublicationType $publicationType)
    {
        $this->responseCode = 200;
        $this->responseData = $publicationType;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PublicationType $publicationType)
    {
        $publicationType->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
