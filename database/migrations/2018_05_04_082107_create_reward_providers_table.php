<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('reward_providers')->insert([
            ['name' => 'Amazon'],
            ['name' => 'Marks and Spencer'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_providers');
    }
}
