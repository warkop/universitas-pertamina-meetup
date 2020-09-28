<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package', function (Blueprint $table) {
            $table->boolean('institution_showed_in_home')->default(false);
            $table->boolean('institution_list')->default(false);
            $table->boolean('institution_detail')->default(false);
            $table->boolean('max_member')->default(false);
            $table->boolean('edit_member_profile')->default(false);
            $table->boolean('user_list')->default(false);
            $table->boolean('user_detail')->default(false);
            $table->boolean('user_advance_search')->default(false);
            $table->boolean('announcement')->default(false);
            $table->boolean('member_showed_in_home')->default(false);
            $table->boolean('posting_opportunity')->default(false);
            $table->boolean('opportunity_list')->default(false);
            $table->boolean('opportunity_detail')->default(false);
            $table->boolean('opportunity_advance_search')->default(false);
            $table->boolean('regulation')->default(false);
            $table->integer('subscription_periode')->nullable();
            $table->boolean('renewal')->default(false);
            $table->float('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package', function (Blueprint $table) {
            $table->dropColumn('institution_showed_in_home');
            $table->dropColumn('institution_list');
            $table->dropColumn('institution_detail');
            $table->dropColumn('max_member');
            $table->dropColumn('edit_member_profile');
            $table->dropColumn('user_list');
            $table->dropColumn('user_detail');
            $table->dropColumn('user_advance_search');
            $table->dropColumn('announcement');
            $table->dropColumn('member_showed_in_home');
            $table->dropColumn('posting_opportunity');
            $table->dropColumn('opportunity_list');
            $table->dropColumn('opportunity_detail');
            $table->dropColumn('opportunity_advance_search');
            $table->dropColumn('regulation');
            $table->dropColumn('subscription_periode');
            $table->dropColumn('renewal');
            $table->dropColumn('price');
        });
    }
}
