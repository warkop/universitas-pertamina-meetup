<?php

namespace App\Policies;

use App\Exceptions\AuthorizationException;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResearchUserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function basic(User $currentUser, Member $member)
    {
        if ($currentUser->type == 1) {
            return false;
        }
        if ($currentUser->type == 0) {
            $memberDepartment = Department::findOrFail($member->department_id);
            $memberInsitution = Institution::findOrFail($currentUser->owner_id);
            if ($memberInsitution->id != $memberDepartment->institution_id) {
                return false;
            }
        }

        return true;
    }
}
