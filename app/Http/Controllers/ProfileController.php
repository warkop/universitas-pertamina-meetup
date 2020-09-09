<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileInstitutionStoreRequest;
use App\Http\Requests\ProfileMemberStoreRequest;

use App\Http\Resources\ProfileInstitutionDataResource;
use App\Http\Resources\ProfileMemberDataResource;

use App\Models\Institution;
use App\Models\Member;
use App\Models\MemberSkill;
use App\Models\MemberEducation;
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
        } else if ($user->type == 1) {
            $data = Member::with('memberSkill')->with('memberEducation')->with('department')->with('nationality')->find($user->owner_id);
            // $this->responseData = $data;
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
         // $institution->email = $request->input('email');
         // $institution->address = $request->input('address');
         // $institution->phone = $request->input('phone');
         $institution->est = $request->input('est');

         $file = $request->file('photo');
         if (!empty($file)) {
            if ($file->isValid()) {
               $changedName = time().rand(100,999).$file->getClientOriginalName();
               $file->storeAs('profile/institution/' . $institution->id, $changedName);

               if ($institution->path_photo != ''){
                  unlink(storage_path('app/profile/institution/').$institution->id.'/'.$institution->path_photo);
               }

               $institution->path_photo = $changedName;
            }
         }

         $institution->save();

         $this->responseCode = 200;
         $this->responseMessage = 'Data berhasil disimpan';
         $this->responseData = $institution;

         return response()->json($this->getResponse(), $this->responseCode);
     }

     public function storeMember(ProfileMemberStoreRequest $request, Member $member)
     {
         $request->validated();

         $member->name = $request->input('name');
         // $member->email = $request->input('email');
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

         $arrayEducation = [
            'member_id' => $member->id,
            'm_ac_degree_id' => $education['degree_id'],
            'institution_name' => $education['institution'],
         ];

         $memberEducation = MemberEducation::where('member_id', $member->id)->first();

         if (!empty($memberEducation)){
             MemberEducation::where('member_id', $member->id)
            ->update($arrayEducation);
         } else {
            MemberEducation::insert($arrayEducation);
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

         //PHOTO//
         $file = $request->file('photo');
         if (!empty($file)) {
            if ($file->isValid()) {
               $changedName = time().rand(100,999).$file->getClientOriginalName();
               $file->storeAs('profile/member/' . $member->id, $changedName);

               if ($member->path_photo != ''){
                  unlink(storage_path('app/profile/member/').$member->id.'/'.$member->path_photo);
               }

               $member->path_photo = $changedName;
            }
         }

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
        return response()->file($path);
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
