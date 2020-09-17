<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Resources\RoleListDataResource;
use App\Http\Resources\RoleDetailDataResource;
use App\Http\Resources\MasterSelectListDataResource;
use App\Http\Requests\MasterListRequest;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $model = Role::get();

      return DataTables::of(RoleListDataResource::collection($model))->toJson();
    }

    public function selectList(MasterListRequest $request)
    {
      $request->validated();
      $limit = strip_tags(request()->get('length'));
      $search = strip_tags(request()->get('search_value'));
      $active_only = strip_tags(request()->get('active_only'));

      $model = Role::select('*');

      if ($limit != null || $limit != ''){
         $model = $model->limit($limit);
      }

      if (!empty($search)) {
          $model = $model->where(function ($where) use ($search) {
             $where->where('name', 'ILIKE', '%' . $search . '%');
          });
      }

      if ($active_only == 1) {
         $model = $model->where('status', 1);
      }

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
        $menu_all = $request->input('menu_all');
        //SPECIFIED MENU
        if ($menu_all == 0){
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
      } elseif ($menu_all == 1) {
         RoleMenu::where('role_id', $role->id)->delete();

         $dataMenu = Menu::get();

         foreach ($dataMenu as $key => $value) {
            $arrayMenu[] = [
               'menu_id' => $value['id'],
               'role_id' => $role->id,
               'action'  => $value['action']
            ];
         };

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

    public function getAll()
    {
        $role = Role::all()->makeHidden(['created_at', 'updated_at', 'created_by', 'updated_by']);

        $this->responseCode = 200;
        $this->responseData = $role;

        return response()->json($this->getResponse(), $this->responseCode);
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
