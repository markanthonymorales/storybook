<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewColumnToBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('book_format')->nullable();
            $table->integer('paper')->nullable();
            $table->integer('cover')->nullable();
            $table->string('lamination')->nullable();
            $table->integer('page_number')->nullable();
            $table->string('colored_index')->nullable();
            
            $table->double('width_front_cover')->nullable();
            $table->double('height_front_cover')->nullable();
            $table->double('spine')->nullable();
            $table->double('bleed')->nullable();
            $table->double('total_width')->nullable();
            $table->double('total_width_incl_bleed')->nullable();
            $table->double('total_height')->nullable();
            $table->double('total_height_incl_bleed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('format');
            $table->dropColumn('paper');
            $table->dropColumn('cover');
            $table->dropColumn('lamination');
            $table->dropColumn('page_number');
            
            $table->dropColumn('width_front_cover');
            $table->dropColumn('height_front_cover');
            $table->dropColumn('spine');
            $table->dropColumn('bleed');
            $table->dropColumn('total_width');
            $table->dropColumn('total_width_incl_bleed');
            $table->dropColumn('total_height');
            $table->dropColumn('total_height_incl_bleed');
        });
    }
}
