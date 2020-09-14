<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMemberAddPathFileDescOrcidIdScopusIdWeb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member', function (Blueprint $table) {
           $table->string('path_photo')->nullable();
           $table->text('orcid_id')->nullable();
           $table->text('scopus_id')->nullable();
           $table->text('desc')->nullable();
           $table->text('web')->nullable();
           $table->string('position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member', function (Blueprint $table) {
            $table->dropColumn('path_photo');
            $table->dropColumn('orcid_id');
            $table->dropColumn('scopus_id');
            $table->dropColumn('desc');
            $table->dropColumn('web');
            $table->dropColumn('position');
        });
    }
}
