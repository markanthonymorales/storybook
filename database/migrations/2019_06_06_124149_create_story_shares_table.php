<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorySharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('story_shares', function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->integer('story_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('is_allow_edit')->default(false);
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('story_shares');
    }
}
