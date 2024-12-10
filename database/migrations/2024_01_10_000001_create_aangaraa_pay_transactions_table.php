<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAangaraaPayTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('aangaraa_pay_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('app_key');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('XAF');
            $table->string('phone_number');
            $table->text('description');
            $table->string('operator');
            $table->string('status');
            $table->string('provider_reference')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('pay_token')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['app_key', 'transaction_id']);
            $table->index(['provider_reference']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('aangaraa_pay_transactions');
    }
}
