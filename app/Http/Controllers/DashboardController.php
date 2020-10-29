<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityListDataResource;
use App\Http\Resources\MemberDashboardResource;
use App\Http\Resources\AnnouncementDashboardResource;
use App\Http\Resources\ProfileInstitutionDataResource;
use App\Models\Announcement;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Opportunity;
use App\Models\Package;
use App\Models\Regulation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getAnnouncement()
    {
        $limit = request()->get('limit');
        $announcement = Announcement::take($limit)->latest()->get();

        $this->responseCode = 200;
        $this->responseData = AnnouncementDashboardResource::collection($announcement);

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
        $this->responseData = OpportunityListDataResource::collection($opportunity->load(['institution', 'opportunityType']));


        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getNewPayment()
    {
        $type = request()->type??1;

        $countOfPayment = Invoice::join('user', 'user.id', '=', 'user_id')
        ->where('type', $type)
        ->whereNotNull('payment_date')
        ->whereNull('valid_until')
        ->count();

        $this->responseData['total'] = $countOfPayment;
        $this->responseData['_link'] = [
            'method' => 'GET',
            'link' => url('api/payment?type='.$type)
        ];
        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getOpeningOpportunity()
    {
        $type = request()->type;
        $countOfOpportunity = Opportunity::whereDate(
            'start_date', '<=', now()
        )->whereDate(
            'end_date', '>=', now()
        );

        if ($type) {
            $countOfOpportunity = $countOfOpportunity->where('opportunity_type_id', $type);
        }
        $countOfOpportunity = $countOfOpportunity->count();

        $this->responseData = $countOfOpportunity;
        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getInstitutional()
    {
        $limit = request()->get('limit');
        $user = auth()->user();
        if ($user->type == 2) {
            $institution = Institution::get()->take($limit)->shuffle();
        } else {
            $invoice = (new Invoice)->getLastPaidedInvoice($user);
            $package = Package::find($invoice->package_id);
            $institution = Institution::take($package->institution_showed_in_home)->get()->take($limit)->shuffle();
        }

        $this->responseCode = 200;
        $this->responseData = $institution;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function getMember()
    {
        $limit = request()->get('limit');
        $user = auth()->user();
        if ($user->type == 2) {
            $member = new Member();
        } else {
            $invoice = (new Invoice)->getLastPaidedInvoice($user);
            $package = Package::find($invoice->package_id);
            $member = Member::take($package->member_showed_in_home);
        }

        // 0=institution, 1=researcher
        if ($user->type == 0) {
            $institution = Institution::find($user->owner_id);

            $member = $member->whereHas('department', function($query) use($institution){
                $query->where('institution_id', $institution->id);
            });
        } else if ($user->type == 1) {
            $memberOld = Member::with('department')->find($user->owner_id);
            $institution_id = $memberOld->department['institution_id'];

            $member = $member->whereHas('department', function($query) use($institution_id){
                $query->where('institution_id', $institution_id);
            });
        }

        $date = Carbon::today()->subYears(3);
        $member = $member->withCount([
           'publication as publication_count' => function ($query) use ($date) {
             $query->where('member_publication.created_at', '>=', $date);
          }]);

        $member = $member->get()->take($limit)->shuffle();

        $this->responseCode = 200;
        $this->responseData = MemberDashboardResource::collection($member);

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

    public function profileInstitution(Institution $institution)
    {
        $this->responseCode = 200;
        $this->responseData = new ProfileInstitutionDataResource($institution->load('department'));

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
