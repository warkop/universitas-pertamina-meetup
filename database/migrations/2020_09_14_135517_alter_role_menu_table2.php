<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRoleMenuTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('role_menu', 'user_id')) {
            Schema::table('role_menu', function (Blueprint $table) {
                $table->renameColumn('user_id', 'role_id');
            });
        }

        if (!Schema::hasColumn('role_menu', 'role_id')) {
            Schema::table('role_menu', function (Blueprint $table) {
                $table->integer('role_id');
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
        Schema::table('role_menu', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });

        if (!Schema::hasColumn('role_menu', 'user_id')) {
            Schema::table('role_menu', function (Blueprint $table) {
                $table->integer('user_id');
            });
        }
    }
}
