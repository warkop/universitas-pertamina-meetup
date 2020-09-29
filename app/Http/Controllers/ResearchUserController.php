<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationRequest;
use App\Http\Requests\changeInstitutionRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\ProfileInstitutionDataResource;
use App\Http\Resources\ProfileMemberDataResource;
use App\Mail\Invitation;
use App\Models\Institution;
use App\Models\Member;
use App\Models\MemberPublication;
use App\Models\MemberSkill;
use App\Models\Skill;
use App\Models\User;
use App\Models\ChangeDept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

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
            $user->save();

            $this->responseCode = 200;
            $this->responseMessage = 'Member berhasil dikonfirmasi';
        } else {
            $this->responseCode = 403;
            $this->responseMessage = 'Member sudah dikonfirmasi';
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

    public function changeInstitution(changeInstitutionRequest $request, Member $member)
    {
      $request->validated();

      $member->department_id = $request->input('department');

      $files = $request->file('files');
      if (!empty($files)) {
         foreach ($files as $key => $value) {
            $changeDept = new ChangeDept();

            $changedName = time().random_int(100,999).$value->getClientOriginalName();

            $value->storeAs('change-dept/' . $member->id, $changedName);
            // }
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


      return response()->json($this->getResponse(), $this->responseCode);
   }
}
