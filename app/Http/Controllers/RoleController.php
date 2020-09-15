<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Resources\RoleListDataResource;
use App\Http\Resources\RoleDetailDataResource;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
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

            $result = Role::listData($start, $perpage, $search, false, $sort, $field, $options);
            $total = Role::listData($start, $perpage, $search, true, $sort, $field, $options);

            if ($grid == 'datatable') {
                $this->responseData['sEcho'] = $echo;
                $this->responseData["iTotalRecords"] = $total;
                $this->responseData["iTotalDisplayRecords"] = $total;
                $this->responseData["aaData"] = RoleListDataResource::collection($result);
                return response()->json($this->responseData, $this->responseCode);
            } else {
                $this->responseData['role'] = RoleListDataResource::collection($result);
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
     public function store(RoleStoreRequest $request, Role $role)
     {
        $request->validated();
        $status = $request->input('status');
        if ($status != null) {
           $role->status = $request->input('status');
        } else {
           $role->name = $request->input('name');
        }
        $role->save();

        //MENU//
        $menu = $request->input('menu');
        RoleMenu::where('role_id', $role->id)->delete();

        if ($menu != null){
           $arrayMenu = [];
           $arrayParentMenu = [];
           foreach ($menu as $key => $value) {
             $dataMenu = Menu::find($value['id']);

             if ($dataMenu->sub_menu != null){
                if (!in_array($dataMenu->sub_menu, $arrayParentMenu)){
                   $arrayMenu[] = [
                      'menu_id' => $dataMenu->sub_menu,
                      'role_id' => $role->id,
                      'action'  => null
                   ];

                   $arrayParentMenu[] = $dataMenu->sub_menu;
                }
             }

             $arrayAction = implode(",", $value['action']);

             $arrayMenu[] = [
                'menu_id' => $value['id'],
                'role_id' => $role->id,
                'action'  => $arrayAction
             ];
          }

          RoleMenu::insert($arrayMenu);
       }
       ///////////////////////////////////////////////////////////////////////


       $this->responseCode = 200;
       $this->responseMessage = 'Data berhasil disimpan';
       $this->responseData = new RoleDetailDataResource($role);

       return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $this->responseCode = 200;
        $this->responseData = new RoleDetailDataResource($role);
        $this->responseNote = [
           'C' => 'Create',
           'R' => 'Read',
           'U' => 'Update',
           'D' => 'Delete',
           'I' => 'Invite',
           'A' => 'Approve',
           'SA'=> 'Select Admin',
        ];

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

    public function getAll()
    {
        $role = Role::all()->makeHidden(['created_at', 'updated_at', 'created_by', 'updated_by']);

        $this->responseCode = 200;
        $this->responseData = $role;

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
    public function destroy(Role $role)
    {
        $role->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
