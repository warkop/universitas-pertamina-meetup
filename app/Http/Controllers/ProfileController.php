<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileInstitutionStoreRequest;
use App\Http\Requests\ProfileMemberStoreRequest;
use App\Http\Requests\ProfilePhotoStoreRequest;
use App\Http\Requests\ProfileChangeMailStoreRequest;

use App\Http\Resources\ProfileInstitutionDataResource;
use App\Http\Resources\ProfileMemberDataResource;
use App\Http\Resources\ProfileAdminDataResource;
use App\Http\Resources\UserListDataResource;

use App\Services\MailService;
use App\Jobs\SendChangeEmail;

use App\Models\EmailReset;
use App\Models\User;
use App\Models\Institution;
use App\Models\Department;
use App\Models\Member;
use App\Models\Skill;
use App\Models\MemberSkill;
use App\Models\MemberEducation;
use App\Models\MemberPublication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->type == 0) {
           $data = Institution::with('department')->find($user->owner_id);
           $this->responseData = new ProfileInstitutionDataResource($data);
        } else if ($user->type == 1) {
            $data = Member::with('title')->with('memberSkill')->with('memberResearchInterest')->with('memberEducation')->with('department')->with('nationality')->with('publication')->find($user->owner_id);
            $this->responseData = new ProfileMemberDataResource($data);
        } else if ($user->type == 2){
            $this->responseData = new UserListDataResource($user);
        }

        $this->responseCode = 200;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function storeInstitution(ProfileInstitutionStoreRequest $request, Institution $institution)
     {
         $request->validated();
         $institution->country = $request->input('country');
         $institution->city = $request->input('city');
         $institution->address = $request->input('address');
         $institution->postal_code = $request->input('postal_code');
         $institution->phone = $request->input('phone');
         $institution->est = $request->input('est');

         //Department//
         $department = $request->input('department');
         Department::where('institution_id', $institution->id)->delete();

         if ($department != null){
            foreach ($department as $key => $value) {
               $value['id'] = ($value['id'] == '')? null: $value['id'];
               Department::withTrashed()->updateOrCreate(
                  ['institution_id' => $institution->id, 'id' => $value['id']],
                  [
                     'name' => $value['name'],
                     'deleted_at' => null,
                  ]
               );
            }
         }
         //////////////////////////////////////////////////
         $institution->save();

         $this->responseCode = 200;
         $this->responseMessage = 'Data berhasil disimpan';
         $this->responseData = $institution;

         return response()->json($this->getResponse(), $this->responseCode);
     }

     public function storePhoto(ProfilePhotoStoreRequest $request)
     {
        $user = auth()->user();

        if ($user->type == 0) {
           $data = Institution::find($user->owner_id);
        } else if ($user->type == 1 || $user->type == 2) {
           $data = Member::find($user->owner_id);
        }

        $file = $request->file('photo');
        if (!empty($file) && $file->isValid()) {
           $changedName = time().random_int(100,999).$file->getClientOriginalName();
           if ($user->type == 0) {
             $file->storeAs('profile/institution/' . $data->id, $changedName);
           } else if ($user->type == 1) {
             $file->storeAs('profile/member/' . $data->id, $changedName);
           } else if ($user->type == 2) {
             $file->storeAs('profile/sysadmin/' . $data->id, $changedName);
           }

           if ($data->path_photo != ''){
             if ($user->type == 0) {
                unlink(storage_path('app/profile/institution/').$data->id.'/'.$data->path_photo);
             } else if ($user->type == 1) {
                unlink(storage_path('app/profile/member/').$data->id.'/'.$data->path_photo);
             } else if ($user->type == 2) {
                unlink(storage_path('app/profile/sysadmin/').$data->id.'/'.$data->path_photo);
             }
          }

          $data->path_photo = $changedName;
       }

       $data->save();

       $this->responseCode = 200;
       $this->responseMessage = 'Photo berhasil disimpan';
       $this->responseData = $data;

       return response()->json($this->getResponse(), $this->responseCode);
     }

     public function storeMember(ProfileMemberStoreRequest $request, Member $member)
     {
         $request->validated();

         $member->name = $request->input('name');
         $member->desc = $request->input('desc');
         $member->department_id = $request->input('department');
         $member->position = $request->input('position');
         $member->employee_number = $request->input('employee_id');
         $member->nationality_id = $request->input('nationality');
         $member->orcid_id = $request->input('orcid_id');
         $member->scopus_id = $request->input('scopus_id');
         $member->web = $request->input('website');

         //Education//
         $education = $request->input('education');
         MemberEducation::where('member_id', $member->id)->delete();

         foreach ($education as $key => $value) {
            MemberEducation::withTrashed()->updateOrCreate(
               ['member_id' => $member->id, 'academic_degree_id' => $value['degree_id']],
               [
                  'institution_name' => $value['institution'],
                  'deleted_at' => null,
               ]
            );
         }
         //////////////////////////////////////////////////

         //SKILL//
         MemberSkill::where('member_id', $member->id)->delete();
         $array_skill = [];
         foreach ($request->input('skill') as $key => $value) {
            $check_skill = Skill::where('name', $value)->where('type', 1)->first();

            if (!empty($check_skill)){
               $array_skill[] = [
                  'member_id' => $member->id,
                  'skill_id' => $check_skill->id,
               ];
            } else {
               $mSkill = new Skill;

               $mSkill->name = $value;
               $mSkill->type = 1;
               $mSkill->status = 0;
               $mSkill->input = 0;

               $mSkill->save();

               $array_skill[] = [
                  'member_id' => $member->id,
                  'skill_id' => $mSkill->id,
               ];
            }
         }

         foreach ($request->input('interest') as $key => $values) {
            $check_skill = Skill::where('name', $values)->where('type', 0)->first();

            if (!empty($check_skill)){
               $array_skill[] = [
                  'member_id' => $member->id,
                  'skill_id' => $check_skill->id,
               ];
            } else {
               $mSkill = new Skill;

               $mSkill->name = $values;
               $mSkill->type = 0;
               $mSkill->status = 0;
               $mSkill->input = 0;

               $mSkill->save();

               $array_skill[] = [
                  'member_id' => $member->id,
                  'skill_id' => $mSkill->id,
               ];
            }
         }

         MemberSkill::insert($array_skill);
         //////////////////////////////////////////////////

         //Publication//
         $publication = $request->input('publication');
         MemberPublication::where('member_id', $member->id)->delete();

         foreach ($publication as $key => $value) {
            MemberPublication::withTrashed()->updateOrCreate(
               ['member_id' => $member->id, 'id' => $value['id']],
               [
                  'title' => $value['title'],
                  'publication_type_id' => $value['publication_type_id'],
                  'author' => $value['author'],
                  'deleted_at' => null,
               ]
            );
         }
         //////////////////////////////////////////////////
         $member->save();

         $this->responseCode = 200;
         $this->responseMessage = 'Data berhasil disimpan';
         $this->responseData = $member;

         return response()->json($this->getResponse(), $this->responseCode);
     }

    public function showFile()
    {
      $user = auth()->user();

      if ($user->type == 0) {
         $data = Institution::find($user->owner_id);
         $path = storage_path('app/profile/institution/'.$data->id.'/'.$data->path_photo);
      } else if ($user->type == 1) {
         $data = Member::find($user->owner_id);
         $path = storage_path('app/profile/member/'.$data->id.'/'.$data->path_photo);
      } else if ($user->type == 2) {
         $data = Member::find($user->owner_id);
         $path = storage_path('app/profile/sysadmin/'.$data->id.'/'.$data->path_photo);
      }

      if ($data->path_photo == ''){
         $this->responseCode = 404;
         $this->responseStatus = 'File Not Found';
         return response()->json($this->getResponse(), $this->responseCode);
      } else {
         return response()->file($path);
      }
    }

    public function showFileInstitution(Institution $institution)
    {
      $path = storage_path('app/profile/institution/'.$institution->id.'/'.$institution->path_photo);

      if ($institution->path_photo == ''){
         $this->responseCode = 404;
         $this->responseStatus = 'File Not Found';
         return response()->json($this->getResponse(), $this->responseCode);
      } else {
         return response()->file($path);
      }
    }

    public function showFileMember(Member $member)
    {
      $path = storage_path('app/profile/member/'.$member->id.'/'.$member->path_photo);

      if ($member->path_photo == ''){
         $this->responseCode = 404;
         $this->responseStatus = 'File Not Found';
         return response()->json($this->getResponse(), $this->responseCode);
      } else {
         return response()->file($path);
      }
    }

   public function changeMail(ProfileChangeMailStoreRequest $request, $id)
   {
        $request->validated();

        $email = strtolower($request->input('email'));

        $user = auth()->user();

        if ($user->type == 0) {
           $model = Institution::findOrFail($id);
           $type = 'institution';
        } else if ($user->type == 1) {
           $model = Member::findOrFail($id);
           $type = 'member';
        } else if ($user->type == 2) {
           $model = Member::findOrFail($id);
           $type = 'sysadmin';
        }

        $emailReset = EmailReset::withTrashed()->updateOrCreate(
            ['email' => $email, 'type' => 3, 'user_id' => $user->id],
            [
            'token' => Str::random(60),
            'deleted_at' => null,
            'deleted_by' => null,
        ]);
        $model->email = $email;
        $model->save();
        SendChangeEmail::dispatch($model, $emailReset, $type);

        $arrayUser = [
            'new_email'=> $email,
        ];

        User::where('id', $user->id)
            ->update($arrayUser);

        $this->responseCode = 200;
        $this->responseMessage = 'Success Change Email';

     return response()->json($this->getResponse(), $this->responseCode);
   }

   public function approveMail(request $request){
      $token = $request->input('change_email_token');
      $type = $request->input('type');
      $emailReset = EmailReset::where('token', $token)->where('type', 3)->first();
      $this->responseData = [
         'email' => $emailReset->email,
      ];

      if (!$emailReset){
         $this->responseCode = 404;
         $this->responseMessage = 'This token is invalid.';

         return response()->json($this->getResponse(), $this->responseCode);
      } else {
         if ($type == 'institution') {
            $data = Institution::where('email', $emailReset->email)->first();
         } else if ($type == 'member') {
            $data = Member::where('email', $emailReset->email)->first();
         } else if ($type == 'sysadmin') {
            $data = Member::where('email', $emailReset->email)->first();
         }

         if (!empty($data)){
            $arrayUser = [
               'email'=> $emailReset->email,
               'new_email'=> null,
               'email_verified_at' => date("Y-m-d H:i:s"),
            ];

            if ($type == 'institution') {
               User::where('owner_id', $data->id)->where('type', 0)->update($arrayUser);
            } else if ($type == 'member') {
               User::where('owner_id', $data->id)->where('type', 1)->update($arrayUser);
            } else if ($type == 'sysadmin') {
               User::where('owner_id', $data->id)->where('type', 2)->update($arrayUser);
            }

            $emailReset->delete();

            $this->responseCode = 200;
            $this->responseMessage = 'Email berhasil di rubah';

            return response()->json($this->getResponse(), $this->responseCode);
         } else {
            $emailReset->delete();
            $this->responseCode = 404;
            $this->responseMessage = 'This token is invalid.';

            return response()->json($this->getResponse(), $this->responseCode);
         }
      }
   }
}
