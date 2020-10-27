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
      $data = Menu::where('id', '!=', 18)->groupBy('menu.id')->orderBy('order', 'asc')->get();

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

      $this->responseCode = 200;

      return response()->json($this->getResponse(), $this->responseCode);
    }

    public function sidebar(Request $request)
    {
        $user = auth()->user();

        if ($user->type == 0) {
            $modelLogin = Institution::find($user->owner_id);
        } else if ($user->type == 1) {
            $modelLogin = Member::find($user->owner_id);
        } else {
            $modelLogin = null;
        }

        $url = $request->get('url');

        $this->menu = new MenuService;

        $menu = $this->menu->checkMenu($user, $modelLogin, $url);

        if (!empty($menu) || !$url){
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

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
