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
            $table->integer('book_id')->nullable();
            $table->integer('user_id')->nullable();

            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('title')->nullable();
            $table->string('author')->nullable();

            $table->double('total_height')->nullable();
            $table->double('total_width')->nullable();
            $table->double('total_height_incl_bleed')->nullable();
            $table->double('total_width_incl_bleed')->nullable();

            $table->integer('total_book')->nullable();
            $table->integer('total_page')->nullable();
            $table->string('colored_index')->nullable();
            $table->integer('total_colored_page')->nullable();

            $table->integer('paper_id')->nullable();
            $table->integer('binding_id')->nullable();
            $table->integer('cover_id')->nullable();
            $table->integer('lamination_id')->nullable();

            $table->double('manufacturing_cost')->nullable();
            $table->double('retail_price')->nullable();

            $table->string('shipping_option')->nullable();
            $table->double('shipping_price')->nullable();
            $table->double('book_price')->nullable();
            $table->double('total_price')->nullable();

            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('country')->nullable();

            $table->longText('xml_directory')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
