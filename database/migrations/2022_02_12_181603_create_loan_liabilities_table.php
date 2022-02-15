<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanLiabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_liabilities', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_info_id');
            $table->float('disbursed_amount');
            $table->date('disbursed_date');
            $table->float('outstanding_amount');
            $table->float('repayment_amount');
            $table->date('liability_start_date');
            $table->date('liability_end_date');
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
        Schema::dropIfExists('loan_liabilities');
    }
}
