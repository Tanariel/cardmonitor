<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id')->nullable();

            $table->unsignedMediumInteger('amount_in_cents')->default(0);
            $table->tinyInteger('multiplier')->default(1);

            $table->string('type');
            $table->string('charge_reason')->nullable();

            $table->string('name')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('booking_text')->nullable();
            $table->text('description')->nullable();
            $table->string('eref')->nullable();

            $table->dateTime('received_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balances');
    }
}
