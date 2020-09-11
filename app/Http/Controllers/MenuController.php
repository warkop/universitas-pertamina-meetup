<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuStoreRequest;

use App\Http\Resources\MenuDataResource;

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

      $data_by_role = Menu::Select('menu.*')
                           ->whereRaw('sub_menu is null')
                           ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
                           ->where('role_menu.role_id', $user->role_id);

      $data_by_user = Menu::select('menu.*')
                           ->whereRaw('sub_menu is null')
                           ->Join('role_menu_addition', 'role_menu_addition.menu_id', 'menu.id')
                           ->where('role_menu_addition.user_id', $user->id);

      $data  = $data_by_role->union($data_by_user)->groupBy('menu.id')->orderBy('order', 'asc')->get();

      $this->responseData = MenuDataResource::collection($data);


      $this->responseCode = 200;

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
    public function show()
    {

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
    public function destroy($id)
    {
        //
    }
}
