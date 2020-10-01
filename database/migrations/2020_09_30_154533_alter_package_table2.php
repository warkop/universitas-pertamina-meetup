<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPackageTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package', function (Blueprint $table) {
            $table->dropColumn('institution_showed_in_home');
            $table->dropColumn('max_member');
            $table->dropColumn('member_showed_in_home');
            $table->dropColumn('posting_opportunity');
        });

        Schema::table('package', function (Blueprint $table) {
            $table->float('institution_showed_in_home')->nullable();
            $table->integer('max_member')->nullable();
            $table->float('member_showed_in_home')->nullable();
            $table->integer('posting_opportunity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package', function (Blueprint $table) {
            $table->boolean('institution_showed_in_home')->default(false)->change();
            $table->boolean('max_member')->default(false)->change();
            $table->boolean('member_showed_in_home')->default(false)->change();
            $table->boolean('posting_opportunity')->default(false)->change();
        });
    }
}
