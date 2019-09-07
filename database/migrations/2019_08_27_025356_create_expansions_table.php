<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpansionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expansions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('game_id');
            $table->unsignedBigInteger('cardmarket_expansion_id');
            $table->string('name');
            $table->string('abbreviation');
            $table->unsignedInteger('icon');
            $table->date('released_at')->nullable();
            $table->boolean('is_released')->default(false);
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
        Schema::dropIfExists('expansions');
    }
}
