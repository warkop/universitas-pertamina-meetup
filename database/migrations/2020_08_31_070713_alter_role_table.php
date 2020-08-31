<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role', function (Blueprint $table) {
            $table->unsignedInteger('created_by')->nullable()->change();
            $table->unsignedInteger('updated_by')->nullable()->change();
            $table->unsignedInteger('deleted_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role', function (Blueprint $table) {
            $table->unsignedInteger('created_by')->change();
            $table->unsignedInteger('updated_by')->change();
            $table->unsignedInteger('deleted_by')->change();
        });
    }
}
