<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Resources\DepartmentListDataResource;
use App\Models\Department;
use App\Models\Institution;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $institution_id = strip_tags(request()->get('institution_id'));
      $options_institution_login = strip_tags(request()->get('options_institution_login'));

      $model = Department::select(
          'department.*',
          'institution.name as institution_name'
      )
      ->leftJoin('institution', 'institution.id', '=', 'department.institution_id');

      if ($institution_id != null || $institution_id != '') {
          $model = $model->where('institution.id', $institution_id);
      }

      if ($options_institution_login == 1){
         $user = auth()->user();

         $model = $model->where('institution.id', $user->owner_id);
      }

      $model = $model->get();

        return DataTables::of(DepartmentListDataResource::collection($model))->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentStoreRequest $request, Department $department)
    {
        $request->validated();
        $department->name           = $request->input('name');
        $department->institution_id = $request->input('institution_id');
        $department->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';
        $this->responseData = $department;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        $this->responseCode = 200;
        $this->responseData = $department;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getListInstitution()
    {
        $institution = Institution::all();

        $this->responseCode = 200;
        $this->responseData = $institution;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getAll(Request $request)
    {
        $institution_id = $request->get('institution_id');
        $department = Department::where('institution_id', $institution_id)->get()->makeHidden(['created_at', 'updated_at', 'created_by', 'updated_by']);

        $this->responseCode = 200;
        $this->responseData = $department;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $department->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
