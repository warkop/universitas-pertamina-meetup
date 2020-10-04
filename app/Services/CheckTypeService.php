<?php
namespace App\Services;

use App\Models\Department;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Package;
use App\Models\User;

class CheckTypeService
{
   public function chackAvailMember($idDepartment)
   {
      $department = Department::find($idDepartment)->toArray();

      var_dump($department);
   }
}
