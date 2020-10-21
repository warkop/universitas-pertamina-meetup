<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationRequest;
use App\Http\Requests\ChangeInstitutionRequest;
use App\Http\Requests\RoleUserRequest;
use App\Http\Resources\ProfileMemberDataResource;
use App\Jobs\SendAcceptMember;
use App\Jobs\SendDeclineMember;
use App\Jobs\SendInvitation;
use App\Services\MenuService;
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
use Yajra\DataTables\Facades\DataTables;
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
        $user = auth()->user();
        $email = $request->input('email');

        SendInvitation::dispatch($email, $user);

        $this->responseCode = 200;
        $this->responseMessage = 'Undangan berhasil dikirim';

        return response()->json($this->getResponse(), $this->responseCode);
   }

   public function acceptMember(Member $member)
   {
        $user = User::where(['owner_id' => $member->id, 'type' => 1])->firstOrFail();
        if ($user->confirm_at == null) {
            $user->confirm_by = auth()->user()->id;
            $user->confirm_at = now();
            $user->status = 1;
            $user->save();

            SendAcceptMember::dispatch($member, $user->email);

            $this->responseCode = 200;
            $this->responseMessage = 'Member berhasil dikonfirmasi';
        } else {
            $this->responseCode = 403;
            $this->responseMessage = '  Member sudah dikonfirmasi';
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

            SendDeclineMember::dispatch($member, $user->email, $reason);

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
      $menu = $request->input('menu');
      $menuAll = $request->input('menu_all');

      $userModel = User::where('owner_id', $member->id)
                       ->where('type', 1)
                       ->first();

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
         $institution = Department::select('institution.*')
                                  ->leftJoin('institution', 'institution.id', 'department.institution_id')
                                  ->where('department.id', $member->department_id)
                                  ->first();

         $userInstitution = User::select('user.*')
                                ->where('type', 0)
                                ->where('owner_id', $institution->id)
                                ->first();

         $this->menu = new MenuService;

         $data_by_institusi = $this->menu->checkMenu($userInstitution, $institution, null, true);
         $data_by_institusi = $data_by_institusi['menu'];

         $data_by_user_default = $this->menu->getMenuRole($userModel, $member, null, true);

         $arrayMenu = [];
         foreach ($data_by_institusi as $key => $value) {
            $index = array_search($value['id'], array_column($data_by_user_default, 'id'));

            if ($index !== null){
               $actionNotIn = 0;
               $actionTambahan = [];
               foreach ($value['action_role'] as $key => $values) {
                  if (!in_array($values, $data_by_user_default[$index]['action_role'])) {
                     $actionNotIn = 1;

                     $actionTambahan[] = $values;
                  }
               }

               if ($actionNotIn != 0) {
                  $arrayMenu[] = [
                     'menu_id' => $value['id'],
                     'user_id' => $userModel->id,
                     'action'  => implode(',',$actionTambahan)
                  ];
               }
            } else {
               $arrayMenu[] = [
                  'menu_id' => $value['id'],
                  'user_id' => $user->id,
                  'action'  => implode(',',$value['action_role'])
               ];
            }
         }
         // return $data;
         RoleMenuAddition::where('user_id', $userModel->id)->delete();

         RoleMenuAddition::create($arrayMenu);
      }
      ///////////////////////////////////////////////////////////////////////


      $this->responseCode = 200;
      $this->responseMessage = 'Data berhasil disimpan';

      return response()->json($this->getResponse(), $this->responseCode);
   }

   public function roleUser(Member $member)
   {
      $userMember = User::select('user.*')
                       ->where('type', 1)
                       ->where('owner_id', $member->id)
                       ->first();

      $institution = Department::select('institution.*')
                               ->leftJoin('institution', 'institution.id', 'department.institution_id')
                               ->where('department.id', $member->department_id)
                               ->first();

      $userInstitution = User::select('user.*')
                             ->where('type', 0)
                             ->where('owner_id', $institution->id)
                             ->first();

      $this->menu = new MenuService;

      $data_by_role = $this->menu->checkMenu($userInstitution, $institution, null, true);

      $data_by_user_default = $this->menu->getMenuRole($userMember, $member, null, true);

      $data_by_user = Menu::select('menu.*', 'role_menu_addition.action as action_role')
                           ->Join('role_menu_addition', 'role_menu_addition.menu_id', 'menu.id')
                           ->where('role_menu_addition.user_id', $userMember->id)
                           ->orderBy('menu.id')
                           ->get();

      $data_by_user = $this->menu->ResourceCheckMenuRole($data_by_user);


      $returnData = [
         'menu_institusi' => $data_by_role['menu'],
         'menu_role' => $data_by_user_default,
         'menu_user' => $data_by_user,
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
}
