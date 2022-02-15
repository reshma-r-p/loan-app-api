<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('borrower_id');
            $table->string('loan_account');
            $table->string('loan_type')->nullable();
            $table->double('amount');
            $table->integer('term')->nullable();
            $table->string('repayment_type')->default('weekly');
            $table->float('interest_rate');
            $table->text('reason_for_loan')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
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
        Schema::dropIfExists('loan_infos');
    }
}
