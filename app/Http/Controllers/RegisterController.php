<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpInstitutionRequest;
use App\Http\Requests\SignUpResearcherRequest;
use App\Model\Institution;
use App\Model\Member;
use App\Model\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Remove some word for safe username
     *
     * @param string $name Name from request name
     * @return string
     **/
    private function safeUsername(string $name):string
    {
        $username = trim(str_replace(' ', '', strtolower($name)));
        $username = str_replace("'", '', $username);
        $username = str_replace("`", '', $username);
        $username = str_replace(",", '', $username);
        $username = str_replace("’", '', $username);

        return $username;
    }
    /**
     * Creating User
     *
     * @param \Illuminate\Http\Request $request Description
     * @param array $spesific Description
     * @return void
     **/
    private function createUser($request, $spesific)
    {
        $user = new User();

        $username = $this->safeUsername($request->name).$spesific['id'];

        $user->username = $username;
        $user->password = bcrypt($request->password);
        $user->type     = $spesific['type'];
        $user->role_id  = $spesific['role_id'];
        $user->owner_id = $spesific['id'];
        $user->save();

        return $username;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
