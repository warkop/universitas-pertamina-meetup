<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Regulation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegulationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Regulation  $regulation
     * @return mixed
     */
    public function view(User $user, Regulation $regulation)
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
                return $this->deny('Paket yang belum aktif atau gratis tidak diizinkan menambah regulasi!');
            }
            $package = Package::find($invoice->package_id);
            if (!$package) {
                return $this->deny('Paket gratis tidak diizinkan menambah regulasi!');
            }

            return true;
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Regulation  $regulation
     * @return mixed
     */
    public function update(User $user, Regulation $regulation)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Regulation  $regulation
     * @return mixed
     */
    public function delete(User $user, Regulation $regulation)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Regulation  $regulation
     * @return mixed
     */
    public function restore(User $user, Regulation $regulation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Regulation  $regulation
     * @return mixed
     */
    public function forceDelete(User $user, Regulation $regulation)
    {
        //
    }
}
