<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedTinyInteger('language_id');
            $table->unsignedBigInteger('cardmarket_article_id')->nullable()->index();
            $table->dateTime('cardmarket_last_edited')->nullable();
            $table->string('condition');
            $table->decimal('unit_cost', 15, 6)->default(0);
            $table->decimal('unit_price', 15, 6)->default(0);
            $table->decimal('provision', 15, 6)->default(0);
            $table->dateTime('bought_at')->nullable();
            $table->dateTime('exported_at')->nullable();
            $table->dateTime('sold_at')->nullable();
            $table->text('hash')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('card_id')->references('id')->on('cards');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
