<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpInstitutionRequest;
use App\Http\Requests\SignUpResearcherRequest;
use App\Models\Institution;
use App\Models\Member;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Creating User
     *
     * @param \Illuminate\Http\Request $request Description
     * @param array $spesific Description
     * @return string
     **/
    private function createUser($request, $spesific)
    {
        $user = new User();

        $user->username = $request->email;
        $user->password = bcrypt($request->password);
        $user->type     = $spesific['type'];
        $user->role_id  = $spesific['role_id'];
        $user->owner_id = $spesific['id'];
        $user->save();

        return $request->email;
    }

    public function signUpInstitution(SignUpInstitutionRequest $request)
    {
        $request->validated();
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
        $username = $this->createUser($request, $spesific);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $institution->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);
        $this->responseData['username']     = $username;

        return response()->json($this->getResponse(), $this->responseCode);
    }

    public function signUpResearcher(SignUpResearcherRequest $request)
    {
        $request->validated();
        $member = new Member();

        $member->name               = $request->name;
        $member->title_id           = $request->title_id;
        $member->department_id      = $request->department_id;
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
        $username = $this->createUser($request, $spesific);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData['registration'] = $member->makeHidden(['created_by', 'updated_by', 'updated_at', 'id']);
        $this->responseData['username']     = $username;

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
