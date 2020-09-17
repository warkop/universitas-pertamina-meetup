<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityListDataResource;
use App\Models\Announcement;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Opportunity;
use App\Models\Regulation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getAnnouncement()
    {
        $limit = request()->get('limit');
        $announcement = Announcement::take($limit)->latest()->get();

        $this->responseCode = 200;
        $this->responseData = $announcement;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getOpportunity()
    {
        $limit = request()->get('limit');
        $opportunity = Opportunity::whereDate(
            'start_date', '<=', now()
        )->whereDate(
            'end_date', '>=', now()
        )->take($limit)->latest()->get();

        $this->responseCode = 200;
        $this->responseData = OpportunityListDataResource::collection($opportunity);


        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getOpeningOpportunity()
    {
        $countOfOpportunity = Opportunity::whereDate(
            'start_date', '<=', now()
        )->whereDate(
            'end_date', '>=', now()
        )->count();

        $this->responseData = $countOfOpportunity;
        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getInstitutional()
    {
        $limit = request()->get('limit');
        $institution = Institution::take($limit)->get()->shuffle();

        $this->responseCode = 200;
        $this->responseData = $institution;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getMember()
    {
        $limit = request()->get('limit');
        $user = auth()->user();

        $member = Member::take($limit);

        // 0=institution, 1=researcher
        if ($user->type == 0) {
            $institution = Institution::find($user->owner_id);

            $member = $member->whereHas('department', function($query) use($institution){
                $query->where('institution_id', $institution->id);
            });
        } else if ($user->type == 1) {
            $member = Member::with('department')->find($user->owner_id);
            $institution_id = $member->department->institution_id;

            $member = $member->whereHas('department', function($query) use($institution_id){
                $query->where('institution_id', $institution_id);
            });
        }

        $member = $member->get()->shuffle();

        $this->responseCode = 200;
        $this->responseData = $member;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getNewRegulation()
    {
        $date = Carbon::today()->subDays(7);
        $countOfRegulation = Regulation::whereNotNull('publish_date')->where('publish_date', '>=', $date)->count();

        $this->responseCode = 200;
        $this->responseData = $countOfRegulation;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getNewMember()
    {
        $date = Carbon::today()->subDays(7);
        $countOfMember = Member::join('user', 'user.owner_id', '=', 'member.id')
        ->where('confirm_at', '>=', $date)->count();

        $this->responseCode = 200;
        $this->responseData = $countOfMember;

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
