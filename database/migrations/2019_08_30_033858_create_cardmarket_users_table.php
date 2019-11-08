<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardmarketUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cardmarket_users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('cardmarket_user_id')->unique();

            $table->string('username');
            $table->dateTime('registered_at');

            $table->boolean('is_commercial');
            $table->boolean('is_seller');

            $table->string('firstname');
            $table->string('name');
            $table->string('extra')->nullable();
            $table->string('street');
            $table->string('zip');
            $table->string('city');
            $table->string('country');
            $table->string('phone');
            $table->string('email');

            $table->string('vat');
            $table->text('legalinformation');

            $table->unsignedTinyInteger('risk_group');
            $table->string('loss_percentage');
            $table->unsignedSmallInteger('unsent_shipments');
            $table->unsignedSmallInteger('reputation');
            $table->unsignedSmallInteger('ships_fast');
            $table->unsignedInteger('sell_count');
            $table->unsignedInteger('sold_items');
            $table->decimal('avg_shipping_time', 5, 2);
            $table->boolean('is_on_vacation');

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
        Schema::dropIfExists('cardmarket_users');
    }
}
