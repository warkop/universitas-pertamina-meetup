<?php
namespace App\Services;

use App\Models\Institution;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function registerInstitution($request)
    {
        $package_id = $request->input('package_id');
        DB::beginTransaction();
        try {
            $institution = Institution::create($request->all());

            $spesific = [
                'id'        => $institution->id,
                'role_id'   => 3,
                'type'      => 0,
            ];
            $user = User::create([
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'type'      => $spesific['type'],
                'role_id'   => $spesific['role_id'],
                'owner_id'  => $spesific['id'],
            ]);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        $paymentService = new PaymentService;
        return [
            'token' => $paymentService->registerPackage($package_id, $user),
            'model' => $institution
        ];
    }

    public function registerResearcher($request)
    {

    }
}
