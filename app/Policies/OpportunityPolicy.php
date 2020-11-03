<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Opportunity;
use App\Models\Package;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OpportunityPolicy
{
    use HandlesAuthorization;

    private function getInstitutionId($user)
    {
        $typeName = [
            'institution'   => 0,
            'researcher'    => 1,
        ];

        if ($user->type == $typeName['institution']) {
            $institution = Institution::find($user->owner_id);

            $institution_id = $institution->id;
        } else if ($user->type == $typeName['researcher']) {
            $member = Member::findOrFail($user->owner_id);

            $institution_id = $member->department->institution->id;
        }

        return $institution_id;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {

    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Opportunity  $opportunity
     * @return mixed
     */
    public function view(User $user, Opportunity $opportunity)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->type != 2) {
            $invoice = (new Invoice)->getLastPaidedInvoice($user);
            if (!$invoice) {
                return $this->deny('Paket yang belum aktif atau gratis tidak diizinkan menambah opportunity!');
            }
            $package = Package::find($invoice->package_id);
            if (!$package) {
                return $this->deny('Paket gratis tidak diizinkan menambah opportunity!');
            }

            $totalOpportunity = Opportunity::where('institution_id', $user->owner_id)->count();
            if ($totalOpportunity < $package->posting_opportunity) {
                return true;
            } else {
                return $this->deny('Anda sudah melebihi batas untuk membuat opportunity!');
            }
        } else {
            return $this->deny('Anda tidak diizinkan mengakses halaman ini');
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Opportunity  $opportunity
     * @return mixed
     */
    public function update(User $user, Opportunity $opportunity)
    {
        if ($user->type != 2) {
            $invoice = (new Invoice)->getLastPaidedInvoice($user);
            if (!$invoice) {
                return $this->deny('Paket yang belum aktif atau gratis tidak diizinkan mengedit opportunity!');
            }

            if ($opportunity->institution_id != $this->getInstitutionId($user)) {
                return $this->deny('Anda tidak diizinkan mengedit opportunity milik Institusi lain!');
            }

            return true;
        } else {
            return $this->deny('Anda tidak diizinkan mengakses halaman ini');
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Opportunity  $opportunity
     * @return mixed
     */
    public function delete(User $user, Opportunity $opportunity)
    {
        return $user->id == $opportunity->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Opportunity  $opportunity
     * @return mixed
     */
    public function restore(User $user, Opportunity $opportunity)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Opportunity  $opportunity
     * @return mixed
     */
    public function forceDelete(User $user, Opportunity $opportunity)
    {
        //
    }
}
