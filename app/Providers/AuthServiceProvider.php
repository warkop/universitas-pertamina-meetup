<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Member;
use App\Models\Opportunity;
use App\Policies\OpportunityPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ResearchUserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Opportunity::class => OpportunityPolicy::class,
        Member::class => ResearchUserPolicy::class,
        Invoice::class => PaymentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-only', function ($user) {
            return $user->type === 2;
        });
    }
}
