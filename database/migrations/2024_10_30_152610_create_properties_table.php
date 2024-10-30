<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->string('slug');
            $table->text('description')->nullable();
            $table->tinyInteger('num_rooms')->unsigned()->default(1);
            $table->tinyInteger('num_beds')->unsigned()->default(1);
            $table->tinyInteger('num_baths')->unsigned()->default(0);
            $table->integer('mq')->unsigned();
            $table->string('zip', 10);
            $table->string('city', 50);
            $table->string('address', 100);
            $table->string('lat');
            $table->string('long');
            $table->decimal('price', 8, 2)->unsigned();
            $table->enum('type', ['mansion', 'ski-in/out', 'tree-house', 'apartment', 'dome', 'cave', 'cabin', 'lake', 'beach', 'castle'])->default('apartment');
            $table->tinyInteger('floor');
            $table->boolean('available')->default(true);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('properties');
    }
};
