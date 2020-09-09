<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResearchGroupCommentFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('research_group_comment_file', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('research_group_comment_id');
            $table->string('name');
            $table->string('path');
            $table->float('size')->nullable();
            $table->string('ext')->nullable();
            $table->boolean('is_image')->default(false);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('research_group_comment_file');
    }
}
