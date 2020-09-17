<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileInstitutionStoreRequest;
use App\Http\Requests\ProfileMemberStoreRequest;
use App\Http\Requests\ProfilePhotoStoreRequest;

use App\Http\Resources\ProfileInstitutionDataResource;
use App\Http\Resources\ProfileMemberDataResource;

use App\Models\Institution;
use App\Models\Department;
use App\Models\Member;
use App\Models\MemberSkill;
use App\Models\MemberEducation;
use App\Models\MemberPublication;
use Illuminate\Http\Request;

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
           // $this->responseData = $data;
        } else if ($user->type == 1) {
            $data = Member::with('title')->with('memberSkill')->with('memberResearchInterest')->with('memberEducation')->with('department')->with('nationality')->with('publication')->find($user->owner_id);
            $this->responseData = new ProfileMemberDataResource($data);
        } else {
            $this->responseData = $user;
        }

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

         // $file = $request->file('photo');
         // if (!empty($file) && $file->isValid()) {
         //    $changedName = time().random_int(100,999).$file->getClientOriginalName();
         //    $file->storeAs('profile/institution/' . $institution->id, $changedName);
         //
         //    if ($institution->path_photo != ''){
         //        unlink(storage_path('app/profile/institution/').$institution->id.'/'.$institution->path_photo);
         //    }
         //
         //    $institution->path_photo = $changedName;
         // }

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
        } else if ($user->type == 1) {
           $data = Member::find($user->owner_id);
        }

        $file = $request->file('photo');
        if (!empty($file) && $file->isValid()) {
           $changedName = time().random_int(100,999).$file->getClientOriginalName();
           $file->storeAs('profile/institution/' . $data->id, $changedName);

           if ($data->path_photo != ''){
             if ($user->type == 0) {
                unlink(storage_path('app/profile/institution/').$data->id.'/'.$data->path_photo);
             } else if ($user->type == 1) {
                unlink(storage_path('app/profile/member/').$data->id.'/'.$data->path_photo);
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
         $memberEducation = MemberEducation::where('member_id', $member->id)->delete();

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
         $skill = [];
         foreach ($request->input('skill') as $key => $value) {
            $skill[] = [
               'member_id' => $member->id,
               'skill_id' => $value
            ];
         }

         MemberSkill::insert($skill);
         //////////////////////////////////////////////////

         //Publication//
         $publication = $request->input('publication');
         $memberEducation = MemberPublication::where('member_id', $member->id)->delete();

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

         // //PHOTO//
         // $file = $request->file('photo');
         // if (!empty($file) && $file->isValid()) {
         //    $changedName = time().random_int(100,999).$file->getClientOriginalName();
         //    $file->storeAs('profile/member/' . $member->id, $changedName);
         //
         //    if ($member->path_photo != ''){
         //        unlink(storage_path('app/profile/member/').$member->id.'/'.$member->path_photo);
         //    }
         //
         //    $member->path_photo = $changedName;
         // }

         $member->save();

         $this->responseCode = 200;
         $this->responseMessage = 'Data berhasil disimpan';
         $this->responseData = $member;

         return response()->json($this->getResponse(), $this->responseCode);
     }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

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
      }

      if ($data->path_photo == ''){
         $this->responseCode = 404;
         $this->responseStatus = 'File Not Found';
         return response()->json($this->getResponse(), $this->responseCode);
      } else {
         return response()->file($path);
      }
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
