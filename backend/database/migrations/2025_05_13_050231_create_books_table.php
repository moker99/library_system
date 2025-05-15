<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('title')->comment('書名');
            $table->string('isbn')->comment('ISBN 編號');
            $table->foreignId('layer_id')->constrained()->onDelete('cascade')->comment('所層層 ID');
            $table->unsignedInteger('position')->comment('該層內的書本儲位順序，例如第幾本');
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
