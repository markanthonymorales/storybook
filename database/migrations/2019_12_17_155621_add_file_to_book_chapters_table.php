<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileToBookChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('book_chapters', function (Blueprint $table) {
            $table->boolean('is_file_type')->default(0);
            $table->longText('file_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_chapters', function (Blueprint $table) {
            $table->dropColumn('is_file_type');
            $table->dropColumn('file_url');
        });
    }
}
