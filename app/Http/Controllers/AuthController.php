<?php

namespace App\Http\Controllers;

use App\Http\Resources\SidebarMenuDataResource;
use App\Services\MenuService;
use App\Models\Menu;
use App\Models\Member;
use App\Models\Invoice;
use App\Models\Institution;
use App\Models\Department;
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
   protected function respondWithToken($token, $change_mail, $menu, $dataLogin)
   {
      return response()->json([
         'access_token' => $token,
         'token_type' => 'bearer',
         'expires_in' => Auth::factory()->getTTL() * 60,
         'change_mail'=> $change_mail,
         'menu'       => $menu,
         'data_user' =>$dataLogin
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

      if (!$user->email_verified_at) {
         Auth::logout();

         $this->responseCode = 401;
         $this->responseMessage = 'You need to confirm your account. We have sent you an activation code, please check your email.';

         return response()->json($this->getResponse(), $this->responseCode);
      } elseif (!$user->confirm_at && $user->type == 1) {
         Auth::logout();

         $this->responseCode = 402;
         $this->responseMessage = 'Your account not activate, please contact Admin for more information';

         return response()->json($this->getResponse(), $this->responseCode);
      } elseif (!$modelLogin->status && $user->type == 0) {
         Auth::logout();

         $this->responseCode = 402;
         $this->responseMessage = 'Your account not activate, please contact Admin for more information';

         return response()->json($this->getResponse(), $this->responseCode);
      } else {

         $this->menu = new MenuService;

         $menu = $this->menu->checkMenu($user, $modelLogin);

         $change_mail = ($user->new_email != null)? TRUE : FALSE;

         $dataLogin = [
            'type' => $user->type,
            'name' => $name,
         ];

         return $this->respondWithToken($token, $change_mail, $menu, $dataLogin);
      }

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
        return response()->json([
            'payload' => $payload,
            'user' => auth()->user()
        ]);
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
      return $this->respondWithToken(Auth::refresh(), auth()->user()->change_mail, $this->sideBarMenu, auth()->user());
   }
}
