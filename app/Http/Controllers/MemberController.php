<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberSignUpInsitutionRequest;
use App\Http\Requests\MemberStoreRequest;
use App\Model\Institution;
use App\Model\Member;
use App\Model\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Creating User
     *
     * @param Object $member Description
     * @param Integer $role_id Description
     * @return void
     * @throws conditon
     **/
    private function createUser($member, $role_id)
    {
        $user = new User();

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MemberStoreRequest $request)
    {
        $request->validated();
    }

    public function signUpInsitution(MemberSignUpInsitutionRequest $request)
    {
        $request->validated();
        $institution = new Institution();

        $institution->name      = $request->name;
        $institution->address   = $request->address;
        $institution->email     = $request->email;
        $institution->save();

        $role_id = 3;
        // create user
        $this->createUser($institution, $role_id);

        $this->responseCode     = 200;
        $this->responseMessage  = 'Pendaftaran berhasil';
        $this->responseData     = $institution;

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
