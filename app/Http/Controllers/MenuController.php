<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuStoreRequest;

use App\Http\Resources\MenuDataResource;
use App\Http\Resources\SidebarMenuDataResource;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = auth()->user();

      $data = Menu::groupBy('menu.id')->orderBy('order', 'asc')->get();

      $this->responseData = MenuDataResource::collection($data);
      $this->responseNote = [
         'C' => 'Create',
         'R' => 'Read',
         'U' => 'Update',
         'D' => 'Delete',
         'I' => 'Invite',
         'A' => 'Approve',
         'SA'=> 'Select Admin',
      ];
      // $this->responseData = $data;

      $this->responseCode = 200;

      return response()->json($this->getResponse(), $this->responseCode);
    }

    public function sidebar(Request $request)
    {
      $user = auth()->user();
      $url = $request->get('url');

      $data_by_role = Menu::Select('menu.*', 'role_menu.action as action_role')
                           // ->whereRaw('sub_menu is null')
                           ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
                           ->where('role_menu.role_id', $user->role_id);

      if ($url != ''){
         $data_by_role = $data_by_role->where('menu.url', $url);
      } else {
         $data_by_role = $data_by_role->whereRaw('sub_menu is null');
      }

      $data_by_user = Menu::select('menu.*', 'role_menu_addition.action as action_role')
                           // ->whereRaw('sub_menu is null')
                           ->Join('role_menu_addition', 'role_menu_addition.menu_id', 'menu.id')
                           ->where('role_menu_addition.user_id', $user->id);

      if ($url != ''){
         $data_by_user = $data_by_user->where('menu.url', $url);
      } else {
         $data_by_user = $data_by_user->whereRaw('sub_menu is null');
      }

      $data  = $data_by_role->union($data_by_user)->groupBy('menu.id', 'role_menu.action')->orderBy('order', 'asc')->get();

      if (count($data) != 0){
         $this->responseCode = 200;
         $this->responseData = SidebarMenuDataResource::collection($data);
         $this->responseNote = [
            'C' => 'Create',
            'R' => 'Read',
            'U' => 'Update',
            'D' => 'Delete',
            'I' => 'Invite',
            'A' => 'Approve',
            'SA'=> 'Select Admin',
         ];
      } else {
         $this->responseCode = 403;
         $this->responseStatus = 'Unauthorized action.';
         $this->responseMessage = 'Silahkan Hubungi Admin';
         return response()->json($this->getResponse(), $this->responseCode);
      }
      // $this->responseData = $data;



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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // public function show(Request $request)
     // {
     //   $user = auth()->user();
     //
     //   $url = $request->get('url');
     //
     //   $data = Menu::where('menu.url', $url)->first();
     //
     //   $this->responseData = MenuDataResource::collection($data);
     //
     //   $this->responseCode = 200;
     //
     //   return response()->json($this->getResponse(), $this->responseCode);
     // }

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
    public function destroy($id)
    {
        //
    }
}
