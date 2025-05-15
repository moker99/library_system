<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('shelves', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('location')->nullable();
        //     $table->timestamps();
        // });
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('書櫃名稱');
            $table->string('location')->nullable()->comment('位置描述，可選填');
            $table->unsignedInteger('sort_order')->default(0)->comment('書櫃排列順序');
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
        Schema::dropIfExists('shelves');
    }
}
