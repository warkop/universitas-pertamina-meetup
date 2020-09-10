<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMemberEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_education', function (Blueprint $table) {
            $table->renameColumn('m_ac_degree_id', 'academic_degree_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_education', function (Blueprint $table) {
            $table->renameColumn('academic_degree_id', 'm_ac_degree_id');
        });
    }
}
