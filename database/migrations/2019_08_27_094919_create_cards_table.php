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
            $table->unsignedBigInteger('expansion_id');
            $table->unsignedBigInteger('cardmarket_product_id')->index();
            $table->unsignedTinyInteger('reprints_count')->default(0);
            $table->string('name');
            $table->string('website');
            $table->string('image');
            $table->string('number');
            $table->string('rarity');
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
