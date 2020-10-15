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
        $tableName = 'package';
        if (!Schema::hasColumn($tableName, 'order')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->integer('order')->nullable();
            });
        }
        if (Schema::hasColumn($tableName, 'institution_list')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('institution_list');
            });
        }
        if (Schema::hasColumn($tableName, 'institution_detail')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('institution_detail');
            });
        }
        if (Schema::hasColumn($tableName, 'edit_member_profile')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('edit_member_profile');
            });
        }
        if (Schema::hasColumn($tableName, 'user_list')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('user_list');
            });
        }
        if (Schema::hasColumn($tableName, 'user_detail')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('user_detail');
            });
        }
        if (Schema::hasColumn($tableName, 'user_advance_search')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('user_advance_search');
            });
        }
        if (Schema::hasColumn($tableName, 'opportunity_list')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('opportunity_list');
            });
        }
        if (Schema::hasColumn($tableName, 'opportunity_detail')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('opportunity_detail');
            });
        }
        if (Schema::hasColumn($tableName, 'opportunity_advance_search')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('opportunity_advance_search');
            });
        }
        if (Schema::hasColumn($tableName, 'regulation')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('regulation');
            });
        }

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
