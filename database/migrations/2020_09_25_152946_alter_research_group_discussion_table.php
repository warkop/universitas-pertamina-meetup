<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterResearchGroupDiscussionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research_group_discussion', function (Blueprint $table) {
            $table->integer('closed_by')->nullable();
            $table->datetime('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('research_group_discussion', function (Blueprint $table) {
            $table->dropColumn('closed_by');
            $table->dropColumn('closed_at');
        });
    }
}
