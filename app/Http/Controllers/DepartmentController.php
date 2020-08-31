<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Resources\DepartmentListDataResource;
use App\Model\Department;
use App\Model\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
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

            $result = Department::listData($start, $perpage, $search, false, $sort, $field, $options);
            $total = Department::listData($start, $perpage, $search, true, $sort, $field, $options);

            if ($grid == 'datatable') {
                $this->responseData['sEcho'] = $echo;
                $this->responseData["iTotalRecords"] = $total;
                $this->responseData["iTotalDisplayRecords"] = $total;
                $this->responseData["aaData"] = DepartmentListDataResource::collection($result);
                return response()->json($this->responseData, $this->responseCode);
            } else {
                $this->responseData['department'] = DepartmentListDataResource::collection($result);
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
