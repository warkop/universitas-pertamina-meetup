<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('number')->nullable();
            $table->float('price')->nullable();
            $table->datetime('valid_until')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('buyer')->nullable();
            $table->string('bank_account')->nullable();
            $table->float('transfer_amount')->nullable();
            $table->string('payment_attachment')->nullable();
            $table->datetime('payment_date')->nullable();
            $table->datetime('payment_confirm_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('deleted_by');
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
        Schema::dropIfExists('invoice');
    }
}
