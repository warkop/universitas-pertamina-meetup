<?php

namespace App\Http\Controllers;

use App\Http\Resources\ResearchUserListDataResource;
use App\Mail\Invitation;
use App\Models\Member;
use App\Models\MemberPublication;
use App\Models\MemberSkill;
use App\Models\ResearchGroup;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResearchUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rules['grid'] = 'required|in:default,datatable';
        $rules['draw'] = 'required_if:grid,datatable|integer';
        $rules['columns'] = 'required_if:grid,datatable';
        $rules['start'] = 'required|integer|min:0';
        $rules['length'] = 'required|integer|min:1|max:100';
        $rules['options_active_only'] = 'boolean';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseStatus = 'Missing Param';
            $this->responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData['error_log'] = $validator->errors();
        } else {
            $this->responseCode = 200;
            $grid = ($request->input('grid') == 'datatable') ? 'datatable' : 'default';

            if ($grid == 'datatable') {
                $numbcol = $request->get('order');
                $columns = $request->get('columns');

                $echo = $request->get('draw');


                $sort = $numbcol[0]['dir'];
                $field = $columns[$numbcol[0]['column']]['data'];
            } else {
                $order = $request->input('order');

                $sort = $request->input('order_method');
                $field = $request->input('order_column');
            }

            $start = $request->get('start');
            $perpage = $request->get('length');

            $search = $request->get('search_value');
            $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
            $search = preg_replace($pattern, '', $search);

            $options = ['grid' => $grid, 'active_only' => $request->get('options_active_only')];

            $result = ResearchGroup::listData($start, $perpage, $search, false, $sort, $field, $options);
            $total = ResearchGroup::listData($start, $perpage, $search, true, $sort, $field, $options);

            if ($grid == 'datatable') {
                $this->responseData['sEcho'] = $echo;
                $this->responseData["iTotalRecords"] = $total;
                $this->responseData["iTotalDisplayRecords"] = $total;
                $this->responseData["aaData"] = ResearchUserListDataResource::collection($result);
                return response()->json($this->responseData, $this->responseCode);
            } else {
                $this->responseData['research_user'] = ResearchUserListDataResource::collection($result);
                $pagination['row'] = count($result);
                $pagination['rowStart'] = ((count($result) > 0) ? ($start + 1) : 0);
                $pagination['rowEnd'] = ($start + count($result));
                $this->responseData['meta']['start'] = $start;
                $this->responseData['meta']['perpage'] = $perpage;
                $this->responseData['meta']['search'] = $search;
                $this->responseData['meta']['total'] = $total;
                $this->responseData['meta']['pagination'] = $pagination;
            }
        }

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

    public function sendingInvitation(Request $request)
    {
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
    public function show($id)
    {
        $member = Member::with(['department.institution', 'memberSkill'])->find($id);
        $this->responseCode = 200;
        $this->responseData = $member;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
