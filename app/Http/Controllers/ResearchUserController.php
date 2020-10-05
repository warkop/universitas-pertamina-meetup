<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationRequest;
use App\Http\Requests\ChangeInstitutionRequest;
use App\Http\Requests\RoleUserRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\MenuDataResource;
use App\Http\Resources\ProfileInstitutionDataResource;
use App\Http\Resources\ProfileMemberDataResource;
use App\Mail\Invitation;
use App\Models\Institution;
use App\Models\Member;
use App\Models\MemberPublication;
use App\Models\MemberSkill;
use App\Models\Skill;
use App\Models\User;
use App\Models\Menu;
use App\Models\ChangeDept;
use App\Models\Department;
use App\Models\RoleMenuAddition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

use App\Services\MailService;
use App\Services\InstitutionService;

class ResearchUserController extends Controller
{
   /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
   public function index()
   {
      $model = Member::listData();

      return DataTables::of($model)->toJson();
   }

   /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
   public function store(Request $request, Member $member)
   {

      $publication_title  = $request->input('publication_title');
      $publication_type   = $request->input('publication_type');
      $publication_author = $request->input('publication_author');
      $research_interest  = $request->input('research_interest');
      $skill              = $request->input('skill');

      $member->name = $request->input('name');
      $member->email = $request->input('email');
      $member->save();

      if ($publication_title != null) {
         MemberPublication::where('member_id', $member->id)->delete();
         for ($i=0; $i < count($publication_title); $i++) {
            $memberPublication = new MemberPublication();
            $memberPublication->member_id = $member->id;
            $memberPublication->title = $publication_title[$i];
            $memberPublication->author = $publication_author[$i];
            $memberPublication->publication_type_id = $publication_type[$i];
            $memberPublication->save();
         }
      }

      if ($research_interest != null) {
         MemberSkill::where('member_id', $member->id)->delete();
         for ($i=0; $i < count($research_interest); $i++) {
            $memberSkill = new MemberSkill();
            $memberSkill->member_id = $member->id;
            $memberSkill->skill_id = $research_interest[$i];
            $memberSkill->save();
         }
      }

      for ($i=0; $i < count($skill); $i++) {
         $memberSkill = new MemberSkill();
         $memberSkill->member_id = $member->id;
         $memberSkill->skill_id = $skill[$i];
         $memberSkill->save();
      }

      $this->responseCode = 200;
      $this->responseData = $member->refresh();

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function getInterest()
   {
      $skill = Skill::where('type', 0)->get();

      $this->responseCode = 200;
      $this->responseData = $skill;

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function getSkill()
   {
      $skill = Skill::where('type', 1)->get();

      $this->responseCode = 200;
      $this->responseData = $skill;

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function sendingInvitation(InvitationRequest $request)
   {
      $request->validated();
      $email = $request->input('email');

      Mail::to($email)->send(new Invitation());

      $this->responseCode = 200;
      $this->responseMessage = 'Undangan berhasil dikirim';

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function acceptMember(Member $member)
   {
      $user = User::where(['owner_id' => $member->id])->firstOrFail();
      if ($user->confirm_at == null) {
         $user->confirm_by = auth()->user()->id;
         $user->confirm_at = now();
         $user->status = 1;
         $user->save();

         $mail = new MailService;
         $mail->sendApproved($member, $user->email);

         $this->responseCode = 200;
         $this->responseMessage = 'Member berhasil dikonfirmasi';
      } else {
         $this->responseCode = 403;
         $this->responseMessage = 'Member sudah dikonfirmasi';
      }

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function declineMember(Request $request, Member $member)
   {
      $user = User::where(['owner_id' => $member->id])->firstOrFail();
      $reason = $request->input('reason');
      if ($user->status != 2) {
         $user->confirm_by = null;
         $user->confirm_at = null;
         $user->status = 2;
         $user->reason = $reason;
         $user->save();

         $mail = new MailService;
         $mail->sendDecline($member, $user->email, $reason);

         $this->responseCode = 200;
         $this->responseMessage = 'Member berhasil ditolak';
      } else {
         $this->responseCode = 403;
         $this->responseMessage = 'Member sudah ditolak';
      }

      return response()->json($this->getResponse(), $this->responseCode);
   }

   /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
   public function show(Member $member)
   {
      $data = $member->load([
      'title',
      'memberSkill',
      'memberResearchInterest',
      'memberEducation',
      'department',
      'nationality',
      'publication'
      ])
      ->refresh()
      ;
      $this->responseData = new ProfileMemberDataResource($data);
      $this->responseCode = 200;

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function changeInstitution(ChangeInstitutionRequest $request, Member $member)
   {
      $request->validated();

      $idDepartment = $request->input('department');

      $institutionService = new InstitutionService;
      $checkAvail = $institutionService->checkAvailMember($idDepartment);

      $this->responseMessage = 'Institusi Berhasil Dirubah';

      if ($checkAvail == 1){
         $this->responseCode = 400;
         $this->responseMessage = 'Institution Payment Not Complete';
      } elseif ($checkAvail == 2) {
         $member->department_id = $idDepartment;

         $files = $request->file('files');
         if (!empty($files)) {
            foreach ($files as $key => $value) {
               $changeDept = new ChangeDept();

               $changedName = time().random_int(100,999).$value->getClientOriginalName();

               $value->storeAs('change-dept/' . $member->id, $changedName);

               $arrayData = [
               'member_id'       => $member->id,
               'department_id'   => $request->input('department'),
               'path_file'       => $changedName,
               'status'          => 1,
               ];

               $changeDept->create($arrayData);
            }
         }

         $member->save();

         $this->responseCode = 200;
         $this->responseMessage = 'Institusi Berhasil Dirubah';
      } elseif ($checkAvail == 3) {
         $this->responseCode = 401;
         $this->responseMessage = 'Institution Has Max Member';
      }

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function changeRole(RoleUserRequest $request,Member $member)
   {
      $request->validated();
      $idRole = $request->input('role');
      $menu = $request->input('menu');
      $menuAll = $request->input('menu_all');

      $userModel = User::where('type', 1)
                       ->where('owner_id', $member->id)
                       ->first();

      $userModel->role_id = $request->input('role');
      $userModel->save();

      //MENU//
      $menu_all = $menuAll;
      //SPECIFIED MENU
      if ($menu_all == 0){
         RoleMenuAddition::where('user_id', $userModel->id)->delete();

         if ($menu != null){
            $arrayMenu = [];
            $arrayParentMenu = [];
            foreach ($menu as $key => $value) {
               $dataMenu = Menu::find($value['id']);

               if ($dataMenu->sub_menu != null){
                  if (!in_array($dataMenu->sub_menu, $arrayParentMenu)){
                     $arrayMenu[] = [
                     'menu_id' => $dataMenu->sub_menu,
                     'user_id' => $userModel->id,
                     'action'  => null
                     ];

                     $arrayParentMenu[] = $dataMenu->sub_menu;
                  }
               }

               $arrayAction = implode(",", $value['action']);

               $arrayMenu[] = [
               'menu_id' => $value['id'],
               'user_id' => $userModel->id,
               'action'  => $arrayAction
               ];
            }

            RoleMenuAddition::insert($arrayMenu);
         }
      } elseif ($menu_all == 1) {
         RoleMenuAddition::where('user_id', $userModel->id->id)->delete();

         $dataMenu = Menu::get();

         foreach ($dataMenu as $key => $value) {
            $arrayMenu[] = [
            'menu_id' => $value['id'],
            'user_id' => $userModel->id,
            'action'  => $value['action']
            ];
         };

         RolRoleMenuAdditioneMenu::insert($arrayMenu);
      }
      ///////////////////////////////////////////////////////////////////////


      $this->responseCode = 200;
      $this->responseMessage = 'Data berhasil disimpan';

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function roleUser(Member $member)
   {
      $userModel = User::select('user.id as id', 'role.id as role_id', 'role.name as name_role')
                       ->where('type', 1)
                       ->leftJoin('role', 'role.id', 'user.role_id')
                       ->where('owner_id', $member->id)
                       ->first();

      $data_by_user = Menu::select('menu.*', 'role_menu_addition.action as action_role')
                           ->Join('role_menu_addition', 'role_menu_addition.menu_id', 'menu.id')
                           ->where('role_menu_addition.user_id', $userModel->id)
                           ->orderBy('order', 'asc')
                           ->get();

      $data_by_role = Menu::Select('menu.*', 'role_menu.action as action_role')
                           ->Join('role_menu', 'role_menu.menu_id', 'menu.id')
                           ->where('role_menu.role_id', $userModel->role_id)
                           ->orderBy('order', 'asc')
                           ->get();

      $returnData = [
         'Role' => [
            'id'     => $userModel->role_id,
            'name'   => $userModel->name_role,
         ],
         'menu_role' => MenuDataResource::collection($data_by_role),
         'menu_user' => MenuDataResource::collection($data_by_user),
      ];

      $this->responseCode = 200;
      $this->responseMessage = 'Data berhasil disimpan';
      $this->responseData = $returnData;

      return response()->json($this->getResponse(), $this->responseCode);
   }
}
