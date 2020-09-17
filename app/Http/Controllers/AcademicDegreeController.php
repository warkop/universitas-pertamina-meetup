<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicDegreeStoreRequest;
use App\Http\Resources\AcademicDegreeListDataResource;
use App\Http\Resources\MasterSelectListDataResource;
use App\Http\Requests\MasterListRequest;
use App\Models\AcademicDegree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AcademicDegreeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = AcademicDegree::get();

        return DataTables::of(AcademicDegreeListDataResource::collection($model))->toJson();
    }

    public function selectList(MasterListRequest $request)
    {
      $request->validated();
      $limit = strip_tags(request()->get('length'));
      $search = strip_tags(request()->get('search_value'));
      $active_only = strip_tags(request()->get('active_only'));

      $model = AcademicDegree::select('*');

      if ($limit != null || $limit != ''){
         $model = $model->limit($limit);
      }

      if (!empty($search)) {
          $model = $model->where(function ($where) use ($search) {
             $where->where('name', 'ILIKE', '%' . $search . '%');
          });
      }

      // if ($active_only == 1) {
      //    $model = $model->where('status', 1);
      // }

      $model = $model->orderBy('name', 'ASC')->get();

      $this->responseCode = 200;
      $this->responseData = MasterSelectListDataResource::collection($model);


      return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicDegreeStoreRequest $request, AcademicDegree $academicDegree)
    {
        $request->validated();

        $academicDegree->name           = $request->input('name');
        $academicDegree->save();

        $this->responseCode     = 200;
        $this->responseMessage  = 'Data berhasil disimpan';
        $this->responseData     = $academicDegree;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicDegree $academicDegree)
    {
        $this->responseCode = 200;
        $this->responseData = $academicDegree;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicDegree $academicDegree)
    {
        $academicDegree->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
