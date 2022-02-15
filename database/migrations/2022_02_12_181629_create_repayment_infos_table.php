<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepaymentInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repayment_infos', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->integer('loan_info_id');
            $table->float('total_amount_paid');
            $table->float('loan_amount_paid');
            $table->float('fine');
            $table->date('payment_date');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
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
        Schema::dropIfExists('repayment_infos');
    }
}
