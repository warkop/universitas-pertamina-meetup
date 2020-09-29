<?php

namespace App\Http\Controllers;

use App\Http\Resources\SidebarMenuDataResource;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private $sideBarMenu;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $change_mail, $menu)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'change_mail'=> $change_mail,
            'menu'       => $menu
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request()->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

       $user = auth()->user();

       $data_by_role = Menu::Select('menu.*', 'role_menu.action as action_role')
                            ->whereRaw('sub_menu is null')
                            ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
                            ->where('role_menu.role_id', $user->role_id);

       $data_by_user = Menu::select('menu.*', 'role_menu_addition.action as action_role')
                            ->whereRaw('sub_menu is null')
                            ->Join('role_menu_addition', 'role_menu_addition.menu_id', 'menu.id')
                            ->where('role_menu_addition.user_id', $user->id);

       $data  = $data_by_role->union($data_by_user)->groupBy('menu.id', 'role_menu.action')->orderBy('order', 'asc')->get();

       $change_mail = ($user->new_email != null)? TRUE : FALSE;

       return $this->respondWithToken($token, $change_mail, SidebarMenuDataResource::collection($data));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token)->toArray();
        return response()->json($payload);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh(), auth()->user()->change_mail, $this->sideBarMenu);
    }
}
