<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOpportunityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opportunity', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->unsignedInteger('opportunity_type_id')->nullable();
            $table->unsignedTinyInteger('target')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opportunity', function (Blueprint $table) {
            $table->integer('type')->nullable();
            $table->dropColumn('opportunity_type_id');
            $table->dropColumn('target');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
}
