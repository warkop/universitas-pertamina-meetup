<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Resources\RoleListDataResource;
use App\Http\Resources\RoleDetailDataResource;
use App\Http\Resources\UserListDataResource;
use App\Http\Resources\MasterSelectListDataResource;
use App\Http\Requests\MasterListRequest;
use App\Http\Requests\UserStoreRequest;

use App\Models\User;
use App\Models\Institution;
use App\Models\Member;
use App\Models\EmailReset;

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
          $user->email_verified_at = date("Y-m-d H:i:s");
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

      if (!empty($user)){
         $this->responseCode = 200;
         $this->responseData = new UserListDataResource($user);
      } else {
         $this->responseCode = 404;
         // $this->responseData = new UserListDataResource($user);
      }

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
