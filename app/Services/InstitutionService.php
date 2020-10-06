<?php
namespace App\Services;

use App\Models\Department;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;

class InstitutionService
{
   public function checkAvailMember($idDepartment)
   {
      $department = Department::find($idDepartment);

      $totalMember = $this->totalMember($department->institution_id);
      $dateNow = date("Y-m-d");

      $data = User::select('package.max_member')
                  ->leftJoin('invoice', 'invoice.user_id', 'user.id')
                  ->leftJoin('package', 'package.id', 'invoice.package_id')
                  ->where('user.type', 0)
                  ->where('owner_id', $department->institution_id)
                  ->where('invoice.valid_until', '>', $dateNow)
                  ->orderBy('invoice.id', 'DESC')->first();

                  //Type 1 is Payment Not Complete, Type 2 Member Still Avail, type 3 member is full
      $type = 1;
      if (!empty($data)){
         $maxMember = $data->max_member;

         if ($maxMember > $totalMember){
            $type = 2;
         } else {
            $type = 3;
         }
      }

      return $type;
   }

   public function totalMember($idInstitution)
   {
      $data = Institution::with('department')->find($idInstitution);

      $totalMember = 0;

      foreach ($data->department as $key => $value) {
         $member = count($value->member);
         $totalMember =  $totalMember + $member;
      }

      return $totalMember;
   }
}
