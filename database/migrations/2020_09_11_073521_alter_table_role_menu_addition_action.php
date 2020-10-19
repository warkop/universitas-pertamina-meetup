<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRoleMenuAdditionAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('role_menu_addition', 'action')) {
            Schema::table('role_menu_addition', function (Blueprint $table) {
                $table->string('action')->nullable();
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
        if (Schema::hasColumn('role_menu_addition', 'action')) {
            Schema::table('role_menu_addition', function (Blueprint $table) {
                $table->dropColumn('action');
            });
        }
    }
}
