<?php
namespace App\Services;

use App\Models\Department;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class RegisterService
{
    private function createUser($request, array $spesific, bool $autoActive=false)
    {
        if ($autoActive) {
            return User::create([
                'email'     => $request->email,
                'password'  => bcrypt($request->password),
                'type'      => $spesific['type'],
                'role_id'   => $spesific['role_id'],
                'owner_id'  => $spesific['id'],
                'status'    => 1,
                'email_verified_at' => now(),
                'confirm_at' => now(),
            ]);
        } else {
            return User::create([
                'email'     => $request->email,
                'password'  => bcrypt($request->password),
                'type'      => $spesific['type'],
                'role_id'   => $spesific['role_id'],
                'owner_id'  => $spesific['id'],
            ]);
        }

    }

    private function isQuotaAvailable(Department $department, $package_id)
    {
        $countMember = Member::whereHas('department', function($query) use($department){
            $query->where('institution_id', $department->institution_id);
        })->count();

        $package = Package::findOrFail($package_id);

        if ($countMember < $package->max_member) {
            return true;
        }

        return false;
    }

    public function CheckAvaibility(Institution $institution)
    {
        $user = User::where('owner_id', $institution->id)->where('type', 0)->firstOrFail();
        $invoice = (new Invoice)->getLastPaidedInvoice($user);
        $package = Package::findOrFail($invoice->package_id);

        $totalMember = Institution::withCount('memberInstitution')->findOrFail($institution->id);

        if ($totalMember->member_institution_count < $package->max_member) {
            $availability = true;
        } else {
            $availability =  false;
        }

        return [
            'is_available' => $availability,
            'count_of_members' => $totalMember->member_institution_count,
            'max_member' => $package->max_member,
        ];
    }

    private function getInstitutionPackage(Department $department)
    {
        $institution = Institution::findOrFail($department->institution_id);
        $user = User::where('owner_id', $institution->id)->where('type', 0)->first();
        if ($user) {
            $invoice = (new Invoice)->getLastPaidedInvoice($user);
            if (!$invoice) {
                $invoice = (new Invoice)->getLastInvoice($user);
                $package = Package::findOrFail($invoice->package_id);
                if ($package->price > 0) {
                    return false;
                }
                return $invoice->package_id;
            }
            return $invoice->package_id;
        } else {
            return false;
        }
    }

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

            $user = $this->createUser($request, $spesific);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        return [
            'token' => (new PaymentService)->registerPackage($package_id, $user),
            'model' => $institution
        ];
    }

    public function registerResearcher($request)
    {
        DB::beginTransaction();
        try {
            $member = new Member();

            if ($request->department_id) {
                $department = Department::findOrFail($request->department_id);
                $package_id = $this->getInstitutionPackage($department);
                if (!$package_id) {
                    throw new Exception('Institusi tidak memiliki paket atau paket milik institusi belum aktif! Silahkan hubungi administrator untuk info lebih lanjut!', 400);
                }
                $isQuotaAvailable = $this->isQuotaAvailable($department, $package_id);
                if (!$isQuotaAvailable && !$request->is_extension) {
                    throw new Exception('Kuota institusi sudah penuh, silahkan Anda mendaftar sebagai independent atau extension', 403);
                }
                $member->department_id      = $request->department_id;
                if ($request->is_extension) {
                    $member->is_extension = true;
                }
            } else {
                $member->is_independent = true;
                $package_id = $request->input('package_id');
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
            $user = $this->createUser($request, $spesific);

            // register package
            $paymentToken = null;
            if ($request->is_extension || $member->is_independent) {
                $paymentToken = (new PaymentService)->registerPackage($package_id, $user);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json([
                'status' => [
                    'code' => $ex->getCode(),
                    'message' => $ex->getMessage()
                ],
                'data' => null
            ], $ex->getCode());
        }

        return [
            'token' => $paymentToken,
            'model' => $member
        ];
    }

    public function generateUserInstitution($request, int $activationStatus)
    {
        $package_id = $request->package_id;
        DB::beginTransaction();
        try {
            $institution = Institution::create($request->all());

            $spesific = [
                'id'        => $institution->id,
                'role_id'   => 3,
                'type'      => 0,
            ];

            $user = $this->createUser($request, $spesific, true);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        (new PaymentService)->registerPackageAutoConfirm($package_id, $user, $activationStatus);
        return $user;
    }

    public function generateUserResearcher($request, int $activationStatus)
    {
        DB::beginTransaction();
        try {
            $member = new Member();

            if ($request->department_id) {
                $department = Department::findOrFail($request->department_id);
                $package_id = $this->getInstitutionPackage($department);
                if (!$package_id) {
                    throw new Exception('Institusi tidak memiliki paket atau paket milik institusi belum aktif! Silahkan hubungi administrator untuk info lebih lanjut!', 400);
                }
                $isQuotaAvailable = $this->isQuotaAvailable($department, $package_id);
                if (!$isQuotaAvailable && !$request->is_extension) {
                    throw new Exception('Kuota institusi sudah penuh, silahkan Anda mendaftar sebagai independent atau extension', 403);
                }
                $member->department_id      = $request->department_id;
                if ($request->is_extension) {
                    $member->is_extension = true;
                }
            } else {
                $member->is_independent = true;
                $package_id = $request->input('package_id');
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
            $user = $this->createUser($request, $spesific, true);

            // register package
            if ($request->is_extension || $member->is_independent) {
                (new PaymentService)->registerPackageAutoConfirm($package_id, $user, $activationStatus);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json([
                'status' => [
                    'code' => $ex->getCode(),
                    'message' => $ex->getMessage()
                ],
                'data' => null
            ], $ex->getCode());
        }

        return $user;
    }
}
