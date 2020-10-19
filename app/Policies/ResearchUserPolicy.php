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

    private function throwExceptionIfNotPermitted(bool $hasPermission = false, bool $allowExceptions = false, $exceptionMessage = null): bool
    {
        // Only throw when a message is provided, or use the default
        // behaviour provided by policies
        if (!$hasPermission && $allowExceptions && !is_null($exceptionMessage)) {
            throw new AuthorizationException($exceptionMessage);
        }

        return $hasPermission;
    }

    public function basic(User $currentUser, Member $member)
    {
        $currentUser = auth()->user();
        if ($currentUser->type == 1) {
            // throw new AuthorizationException('Anda tidak diizinkan untuk melakukan ini!');
            // abort(403, 'Anda tidak diizinkan untuk melakukan ini!');
            // return $this->deny('Anda tidak diizinkan untuk melakukan ini!');
            return false;
        }
        if ($currentUser->type == 0) {
            $memberDepartment = Department::findOrFail($member->department_id);
            $memberInsitution = Institution::findOrFail($currentUser->owner_id);
            if ($memberInsitution->id != $memberDepartment->institution_id) {
                // return $this->deny('Anda tidak diizinkan untuk melakukan ini!');
                // throw new AuthorizationException();
                // return response()->json('Anda tidak diizinkan untuk melakukan ini!', 403);
                abort(403, 'Anda tidak diizinkan untuk melakukan ini!');
            }
        }

        return true;
    }
}
