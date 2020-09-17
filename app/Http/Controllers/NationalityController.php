<?php

namespace App\Http\Controllers;

use App\Http\Requests\NationalityStoreRequest;
use App\Http\Resources\NationalityListDataResource;
use App\Models\Nationality;
use Yajra\DataTables\Facades\DataTables;

class NationalityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = Nationality::get();

        return DataTables::of(NationalityListDataResource::collection($model))->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NationalityStoreRequest $request, Nationality $nationality)
    {
        $request->validated();
        $nationality->name = $request->input('name');
        $nationality->code = $request->input('code');
        $nationality->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';
        $this->responseData = $nationality;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Nationality $nationality)
    {
        $this->responseCode = 200;
        $this->responseData = $nationality;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getAll()
    {
        $nationality = Nationality::all()->makeHidden(['created_at', 'updated_at', 'created_by', 'updated_by']);

        $this->responseCode = 200;
        $this->responseData = $nationality;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nationality $nationality)
    {
        $nationality->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
