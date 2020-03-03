<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->unsignedMediumInteger('credits')->default(0);
            $table->unsignedMediumInteger('balance_in_cents')->default(0);

            $table->text('prepared_message')->nullable();

            $table->string('locale', 10);

            $table->boolean('is_applying_rules')->default(false);
            $table->boolean('is_syncing_articles')->default(false);
            $table->boolean('is_syncing_orders')->default(false);

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
