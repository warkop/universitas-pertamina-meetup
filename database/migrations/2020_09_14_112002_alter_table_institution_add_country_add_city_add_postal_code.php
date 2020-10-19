<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInstitutionAddCountryAddCityAddPostalCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institution', function (Blueprint $table) {
           $table->unsignedBigInteger('country')->nullable();
           $table->string('city')->nullable();
           $table->string('postal_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('institution', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('city');
            $table->dropColumn('postal_code');
        });
    }
}
