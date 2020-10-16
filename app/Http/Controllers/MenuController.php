<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuStoreRequest;

use App\Http\Resources\MenuDataResource;
use App\Http\Resources\SidebarMenuDataResource;

use App\Models\Menu;
use App\Models\Member;
use App\Models\Institution;
use Illuminate\Http\Request;

use App\Services\MenuService;

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
         'DE'=> 'Detail',
         'AS'=> 'Advanced Seaarch',
      ];
      // $this->responseData = $data;

      $this->responseCode = 200;

      return response()->json($this->getResponse(), $this->responseCode);
    }

    public function sidebar(Request $request)
    {
      $user = auth()->user();

      if ($user->type == 0) {
         $modelLogin = Institution::find($user->owner_id);
         $name = $modelLogin->name;
      } else if ($user->type == 1) {
         $modelLogin = Member::find($user->owner_id);
         $name = $modelLogin->name;
      } else {
         $modelLogin = null;
         $name = $user->email;
      }

      $url = $request->get('url');

      $this->menu = new MenuService;

      $menu = $this->menu->checkMenu($user, $modelLogin, $url);

      if (count($menu) != 0 || !$url){
         $this->responseCode = 200;
         $this->responseData = $menu['menu'];
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
}
