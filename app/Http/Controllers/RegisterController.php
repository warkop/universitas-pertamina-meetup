<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpInstitutionRequest;
use App\Http\Requests\SignUpResearcherRequest;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->paymentService = new PaymentService;
    }
    /**
     * Creating User
     *
     * @param \Illuminate\Http\Request $request Description
     * @param array $spesific Description
     * @return string
     **/
    private function createUser($request, $spesific)
    {
        $user = User::create([
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'type'      => $spesific['type'],
            'role_id'   => $spesific['role_id'],
            'owner_id'  => $spesific['id'],
        ]);

        return $user;
    }

    public function signUpInstitution(SignUpInstitutionRequest $request)
    {
        $request->validated();

        $package_id = $request->input('package_id');

        $institution = new Institution();

        $institution->name      = $request->name;
        $institution->address   = $request->address;
        $institution->email     = $request->email;
        $institution->save();

        $spesific = [
            'id'        => $institution->id,
            'role_id'   => 3,
            'type'      => 0,
        ];
        // create user
        $user = $this->createUser($request, $spesific);

        // register package
        $this->paymentService->registerPackage($package_id, $user);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $institution->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function signUpResearcher(SignUpResearcherRequest $request)
    {
        $request->validated();

        $package_id = $request->input('package_id');

        $member = new Member();

        if ($request->department_id) {
            $member->department_id      = $request->department_id;
        } else {
            $member->is_independent = true;
        }

        $member->name               = $request->name;
        $member->title_id           = $request->title_id;
        $member->nationality_id     = $request->nationality_id;
        $member->employee_number    = $request->employee_number;
        $member->office_address     = $request->office_address;
        $member->office_phone_number= $request->office_phone_number;
        $member->email              = $request->email;
        $member->save();

        $spesific = [
            'id'        => $member->id,
            'role_id'   => 2,
            'type'      => 1,
        ];
        // create user
        $user = $this->createUser($request, $spesific);

        // register package
        $this->paymentService->registerPackage($package_id, $user);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $member->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function uploadPayment(Request $request, Invoice $invoice)
    {
        $result = $this->paymentService->uploadPayment($request->file('attachment'), $invoice);
        if ($result) {
            $this->responseCode     = 200;
            $this->responseMessage  = 'Bukti pembayaran berhasil diunggah';
        } else {
            $this->responseCode     = 400;
            $this->responseMessage  = 'Bukti pembayaran wajib diunggah!';
        }

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
