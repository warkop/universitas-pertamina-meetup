<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMAcDegree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_ac_degree', function (Blueprint $table) {
           $table->id();
           $table->string('name');
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
        Schema::dropIfExists('m_ac_degree');
    }
}
