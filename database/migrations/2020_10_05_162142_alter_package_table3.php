<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPackageTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package', function (Blueprint $table) {
            $table->dropColumn('institution_list');
            $table->dropColumn('institution_detail');
            $table->dropColumn('edit_member_profile');
            $table->dropColumn('user_list');
            $table->dropColumn('user_detail');
            $table->dropColumn('user_advance_search');
            $table->dropColumn('opportunity_list');
            $table->dropColumn('opportunity_detail');
            $table->dropColumn('opportunity_advance_search');
            $table->dropColumn('regulation');
            $table->integer('order')->nullable();
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
            $table->boolean('institution_list')->default(false);
            $table->boolean('institution_detail')->default(false);
            $table->boolean('edit_member_profile')->default(false);
            $table->boolean('user_list')->default(false);
            $table->boolean('user_detail')->default(false);
            $table->boolean('user_advance_search')->default(false);
            $table->boolean('opportunity_list')->default(false);
            $table->boolean('opportunity_detail')->default(false);
            $table->boolean('opportunity_advance_search')->default(false);
            $table->boolean('regulation')->default(false);
            $table->dropColumn('order');
        });
    }
}
