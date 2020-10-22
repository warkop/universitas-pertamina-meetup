<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleUserRequest;
use App\Http\Resources\RoleListDataResource;
use App\Http\Resources\RoleDetailDataResource;
use App\Http\Resources\UserListDataResource;
use App\Http\Resources\MasterSelectListDataResource;
use App\Http\Requests\MasterListRequest;
use App\Http\Requests\UserStoreRequest;

use App\Services\MenuService;

use App\Models\User;
use App\Models\Institution;
use App\Models\Member;
use App\Models\EmailReset;
use App\Models\Menu;
use App\Models\RoleMenuAddition;

use App\Services\MailService;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $model = User::where('type', 2)->get();

      return DataTables::of(UserListDataResource::collection($model))->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(UserStoreRequest $request, user $user)
     {
        $request->validated();
        $status = $request->input('status');

        if ($status != null) {
           $user->status = $request->input('status');

           $user->save();
        } else {
           if ($user->id){
             $member = Member::find($user->owner_id);
          } else {
             $member = new Member;
          }

          $member->email = $request->input('email');
          $member->name = $request->input('name');
          $member->position = $request->input('position');
          $member->nationality_id = $request->input('nationality');
          $member->employee_number = $request->input('employee_id');
          $member->desc = $request->input('desc');
          $member->is_sysadmin = true;
          $member->save();

          $user->email = $request->input('email');
          $user->email_verified_at = date('Y-m-d H:i:s');
          $user->password = bcrypt('meetup123');
          $user->type = 2;
          $user->role_id = ($request->input('role') != null)? $request->input('role') : 1;
          $user->status = 1;
          $user->owner_id = $member->id;
          $user->save();
        }

       $this->responseCode = 200;
       $this->responseMessage = 'Data berhasil disimpan';
       $this->responseData = new UserListDataResource($user);

       return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {
      $user = User::with('member')->where('type', 2)->where('id', $user_id)->first();

      $modelLogin = new \stdClass;
      $modelLogin->status = true;
      $this->menu = new MenuService;

      $menu = $this->menu->checkMenu($user, $modelLogin, null, true);

      $data = [
         'user' => new UserListDataResource($user),
         'menu' => $menu
      ];


      if (!empty($user)){
         $this->responseCode = 200;
         $this->responseData = $data;
      } else {
         $this->responseCode = 404;
      }

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function changeRole(RoleUserRequest $request,User $user)
   {
      $request->validated();
      $menu = $request->input('menu');
      $menuAll = $request->input('menu_all');

      $user->role_id = $request->input('role');

      $user->save();

      //MENU//
      $menu_all = $menuAll;
      //SPECIFIED MENU
      if ($menu_all == 0){
         RoleMenuAddition::where('user_id', $user->id)->delete();

         if ($menu != null){
            $arrayMenu = [];
            $arrayParentMenu = [];
            foreach ($menu as $key => $value) {
                $dataMenu = Menu::find($value['id']);

                if ($dataMenu->sub_menu != null && !in_array($dataMenu->sub_menu, $arrayParentMenu)){
                    $arrayMenu[] = [
                        'menu_id' => $dataMenu->sub_menu,
                        'user_id' => $user->id,
                        'action'  => null
                    ];

                    $arrayParentMenu[] = $dataMenu->sub_menu;
                }

                $arrayAction = implode(',', $value['action']);

                $arrayMenu[] = [
                    'menu_id' => $value['id'],
                    'user_id' => $user->id,
                    'action'  => $arrayAction
                ];
            }

            RoleMenuAddition::insert($arrayMenu);
         }
      } elseif ($menu_all == 1) {

         $this->menu = new MenuService;

         $all_menu = Menu::groupBy('menu.id')->orderBy('order', 'asc')->get();

         $data_by_role = Menu::Select('menu.*', 'role_menu.action as action_role')
         ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
         ->where('role_menu.role_id', $user->role_id)
         ->orderBy('order', 'asc')->get();

         $data_by_role = $this->menu->ResourceCheckMenuRole($data_by_role);


         $arrayMenu = [];
         foreach ($all_menu as $key => $value) {
            $index = array_search($value['id'], array_column($data_by_role, 'id'));

            if ($index !== null){
               $actionNotIn = 0;
               $actionTambahan = [];

               $arrayAction = explode(',', $value['action']);
               foreach ($arrayAction as $key => $values) {
                  if (!in_array($values, $data_by_role[$index]['action_role'])) {
                     $actionNotIn = 1;

                     $actionTambahan[] = $values;
                  }
               }

               if ($actionNotIn != 0) {
                  $arrayMenu[] = [
                     'menu_id' => $value['id'],
                     'user_id' => $user->id,
                     'action'  => implode(',',$actionTambahan)
                  ];
               }
            } else {
               $arrayMenu[] = [
                  'menu_id' => $value['id'],
                  'user_id' => $user->id,
                  'action'  => $value['action']
               ];
            }
         }
         RoleMenuAddition::where('user_id', $user->id)->delete();

         RoleMenuAddition::create($arrayMenu);
      }
      ///////////////////////////////////////////////////////////////////////


      $this->responseCode = 200;
      $this->responseMessage = 'Data berhasil disimpan';

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function detailRoleUser(User $user)
   {
      $this->menu = new MenuService;


      $all_menu = Menu::groupBy('menu.id')->orderBy('order', 'asc')->get();

      $all_menu = $this->menu->ResourceCheckMenuRole($all_menu);

      $menu_role = Menu::Select('menu.*', 'role_menu.action as action_role')
                        ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
                        ->where('role_menu.role_id', $user->role_id)
                        ->orderBy('order', 'asc')->get();


      $menu_role = $this->menu->ResourceCheckMenuRole($menu_role);

      $menu_user = Menu::select('menu.*', 'role_menu_addition.action as action_role')
                           ->Join('role_menu_addition', 'role_menu_addition.menu_id', 'menu.id')
                           ->where('role_menu_addition.user_id', $user->id)
                           ->orderBy('menu.id')
                           ->get();

      $menu_user = $this->menu->ResourceCheckMenuRole($menu_user);


      $returnData = [
         'role'   => [
            'id' => $user->role->id,
            'name' => $user->role->name,
         ],
         'all_menu' => $all_menu,
         'menu_role' => $menu_role,
         'menu_user' => $menu_user,
      ];

      $this->responseCode = 200;
      $this->responseMessage = 'Data berhasil disimpan';
      $this->responseData = $returnData;
      $this->responseNote = [
         'C' => 'Create',
         'R' => 'Read',
         'U' => 'Update',
         'D' => 'Delete',
         'I' => 'Invite',
         'A' => 'Approve',
         'SA'=> 'Select Admin',
         'DE'=> 'Detail',
         'AS'=> 'Advanced Seaarch',
      ];

      return response()->json($this->getResponse(), $this->responseCode);
   }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $member = Member::find($user->owner_id);
        $member->delete();
        $user->delete();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil dihapus';

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
