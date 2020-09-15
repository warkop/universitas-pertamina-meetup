<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstitutionListDataResource;
use App\Http\Resources\OpportunityListDataResource;
use App\Models\Announcement;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Opportunity;
use App\Models\Regulation;

class DashboardController extends Controller
{
    public function getAnnouncement()
    {
        $limit = request()->get('limit');
        $announcement = Announcement::take($limit)->get();

        $this->responseCode = 200;
        $this->responseData = $announcement;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getOpeningOpportunity()
    {
        $limit = request()->get('limit');
        $opportunity = Opportunity::whereDate(
            'start_date', '<=', now()
        )->whereDate(
            'end_date', '>=', now()
        )->take($limit)->latest()->get();

        $countOfOpportunity = Opportunity::whereDate(
            'start_date', '<=', now()
        )->whereDate(
            'end_date', '>=', now()
        )->count();

        $this->responseCode                 = 200;
        $this->responseData['oppportunity'] = OpportunityListDataResource::collection($opportunity);
        $this->responseData['count']        = $countOfOpportunity;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getInstitutional()
    {
        $limit = request()->get('limit');
        $institution = Institution::take($limit)->get();

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

        $member = $member->latest()->get();

        $this->responseCode = 200;
        $this->responseData = $member;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getRegulation()
    {
        $regulation = Regulation::get();

        $this->responseCode = 200;
        $this->responseData = $regulation;

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
