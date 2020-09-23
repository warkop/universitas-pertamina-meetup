<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEmailReset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_reset', function (Blueprint $table) {
           $table->string('email');
           $table->string('token');
           $table->unsignedInteger('created_by')->nullable();
           $table->unsignedInteger('updated_by')->nullable();
           $table->unsignedInteger('deleted_by')->nullable();
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
        Schema::dropIfExists('email_reset');
    }
}
