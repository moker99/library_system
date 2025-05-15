<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('layers', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('shelf_id')->constrained()->onDelete('cascade');
        //     $table->unsignedInteger('level'); // 第幾層
        //     $table->timestamps();
        // });
        Schema::create('layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelf_id')->constrained()->onDelete('cascade')->comment('所屬書櫃 ID');
            $table->unsignedInteger('level')->comment('第幾層');
            $table->unsignedInteger('capacity')->default(10)->comment('該層最大可容納書籍數量');
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
        Schema::dropIfExists('layers');
    }
}
