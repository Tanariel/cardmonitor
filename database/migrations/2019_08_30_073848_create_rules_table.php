<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');

            $table->unsignedTinyInteger('game_id');

            $table->unsignedBigInteger('expansion_id')->nullable();

            $table->string('name');
            $table->text('description')->nullable();

            $table->boolean('active')->default(false);

            $table->string('rarity')->nullable();
            $table->string('condition')->nullable();

            $table->unsignedSmallInteger('order_column')->default(0);

            $table->string('base_price');

            $table->decimal('multiplier', 15, 6)->default(1);
            $table->string('formular')->nullable();

            $table->decimal('price_above', 15, 6)->default(0);
            $table->decimal('price_below', 15, 6)->default(100000000);

            $table->decimal('min_price_masterpiece', 15, 6)->default(0);
            $table->decimal('min_price_mythic', 15, 6)->default(0);
            $table->decimal('min_price_rare', 15, 6)->default(0);
            $table->decimal('min_price_special', 15, 6)->default(0);
            $table->decimal('min_price_time_shifted', 15, 6)->default(0);
            $table->decimal('min_price_uncommon', 15, 6)->default(0);
            $table->decimal('min_price_common', 15, 6)->default(0);
            $table->decimal('min_price_land', 15, 6)->default(0);
            $table->decimal('min_price_token', 15, 6)->default(0);
            $table->decimal('min_price_tip_card', 15, 6)->default(0);

            $table->boolean('is_foil')->default(false);
            $table->boolean('is_signed')->default(false);
            $table->boolean('is_altered')->default(false);
            $table->boolean('is_playset')->default(false);

            $table->boolean('has_masterpiece')->default(true);
            $table->boolean('has_mythic')->default(true);
            $table->boolean('has_rare')->default(true);
            $table->boolean('has_special')->default(true);
            $table->boolean('has_time_shifted')->default(true);
            $table->boolean('has_uncommon')->default(true);
            $table->boolean('has_common')->default(true);
            $table->boolean('has_land')->default(true);
            $table->boolean('has_token')->default(true);
            $table->boolean('has_tip_card')->default(true);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('rules');
    }
}
