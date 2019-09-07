<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shipping_method_id');

            $table->unsignedBigInteger('cardmarket_order_id');
            $table->unsignedBigInteger('cardmarket_buyer_id');

            $table->string('state');
            $table->dateTime('bought_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->string('canceled_reason')->nullable();
            $table->string('shippingmethod');

            $table->unsignedTinyInteger('evaluation_grade')->nullable();
            $table->unsignedTinyInteger('evaluation_item_description')->nullable();
            $table->unsignedTinyInteger('evaluation_packaging')->nullable();
            $table->string('evaluation_comment')->nullable();

            $table->decimal('provision', 15, 6)->default(0);

            $table->decimal('items_cost', 15, 6)->default(0);

            $table->decimal('articles_revenue', 15, 6)->default(0);
            $table->decimal('articles_cost', 15, 6)->default(0);
            $table->decimal('articles_profit', 15, 6)->default(0);

            $table->decimal('shipment_revenue', 15, 6)->default(0);
            $table->decimal('shipment_cost', 15, 6)->default(0);
            $table->decimal('shipment_profit', 15, 6)->default(0);

            $table->decimal('revenue', 15, 6)->default(0);
            $table->decimal('cost', 15, 6)->default(0);
            $table->decimal('profit', 15, 6)->default(0);

            $table->unsignedInteger('articles_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
