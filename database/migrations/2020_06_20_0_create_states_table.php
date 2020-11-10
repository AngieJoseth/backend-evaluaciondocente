<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatesTable extends Migration
{
    public function up()
    {
        Schema::connection('pgsql-ignug')->create('states', function (Blueprint $table) {
            $table->id();
            $table->enum('code', ['1', '2', '3', '4']);
            $table->string('name');
        });
    }


    public function down()
    {
        Schema::connection('pgsql-ignug')->dropIfExists('states');
    }
}
