<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('game_id');
            $table->unsignedBigInteger('expansion_id');
            $table->unsignedBigInteger('cardmarket_product_id')->index();
            $table->string('skryfall_card_id')->nullable();
            $table->unsignedSmallInteger('reprints_count')->default(0);
            $table->string('name');
            $table->string('website');
            $table->string('image');
            $table->string('number')->nullable();
            $table->string('rarity');
            $table->unsignedInteger('articles_count')->default(0);
            $table->unsignedInteger('articles_foil_count')->default(0);

            $table->decimal('price_sell', 7, 2)->default(0);
            $table->decimal('price_low', 7, 2)->default(0);
            $table->decimal('price_trend', 7, 2)->default(0);
            $table->decimal('price_avg', 7, 2)->default(0);
            $table->decimal('price_german_pro', 7, 2)->default(0);
            $table->decimal('price_foil_sell', 7, 2)->default(0);
            $table->decimal('price_foil_low', 7, 2)->default(0);
            $table->decimal('price_foil_trend', 7, 2)->default(0);
            $table->decimal('price_low_ex', 7, 2)->default(0);

            $table->dateTime('prices_updated_at')->nullable();

            $table->timestamps();

            $table->foreign('expansion_id')->references('id')->on('expansions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
