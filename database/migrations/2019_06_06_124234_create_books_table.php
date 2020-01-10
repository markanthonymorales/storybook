<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->longText('description')->nullable();
            $table->string('tags')->nullable();

            $table->double('price')->default(0);
            $table->double('original_price')->default(0);
            $table->double('markup_price')->default(0);
            $table->double('ebook_price')->default(0);
            $table->double('ebook_markup_price')->default(0);

            $table->string('status_type')->nullable();

            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->date('book_date')->nullable();

            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_save_as_draft')->default(false);

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
        Schema::dropIfExists('books');
    }
}
