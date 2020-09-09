<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMemberEducation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_education', function (Blueprint $table) {
           $table->id();
           $table->unsignedBigInteger('member_id');
           $table->unsignedBigInteger('m_ac_degree_id');
           $table->string('institution_name');
           $table->unsignedBigInteger('created_by')->nullable();
           $table->unsignedBigInteger('updated_by')->nullable();
           $table->unsignedBigInteger('deleted_by')->nullable();
           $table->timestamps();
           $table->softDeletes();
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
            //
        });
    }
}
