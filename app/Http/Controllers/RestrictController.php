<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpInstitutionRequest;
use App\Http\Requests\SignUpResearcherRequest;
use App\Services\RegisterService;
use Illuminate\Http\Request;

class RestrictController extends Controller
{
    private $active = 1;
    private $almostExpired = 2;
    private $expired = 3;

    public function createActiveUserInstitution(SignUpInstitutionRequest $request)
    {
        $request->validated();
        $this->responseData = (new RegisterService)->generateUserInstitution($request, $this->active);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function createAlmostExpiredUserInstitution(SignUpInstitutionRequest $request)
    {
        $request->validated();
        $this->responseData = (new RegisterService)->generateUserInstitution($request, $this->almostExpired);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function createExpiredUserInstitution(SignUpInstitutionRequest $request)
    {
        $request->validated();
        $this->responseData = (new RegisterService)->generateUserInstitution($request, $this->expired);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function createActiveUserResearcher(SignUpResearcherRequest $request)
    {
        $request->validated();
        $this->responseData = (new RegisterService)->generateUserResearcher($request, $this->active);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function createAlmostExpiredUserResearcher(SignUpResearcherRequest $request)
    {
        $request->validated();
        $this->responseData = (new RegisterService)->generateUserResearcher($request, $this->almostExpired);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function createExpiredUserResearcher(SignUpResearcherRequest $request)
    {
        $request->validated();
        $this->responseData = (new RegisterService)->generateUserResearcher($request, $this->expired);

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
