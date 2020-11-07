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
            $table->string('code')->unique()->comment('No debe ser modificado una vez que se lo crea');
            $table->string('name');
        });
    }


    public function down()
    {
        Schema::connection('pgsql-ignug')->dropIfExists('states');
    }
}
