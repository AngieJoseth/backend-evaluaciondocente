<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressTable extends Migration
{
    public function up()
    {
        Schema::connection('pgsql-ignug')->create('address', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('catalogues');
            $table->double('latitud')->nullable();
            $table->double('longitud')->nullable();
            $table->string('main_street')->nullable();
            $table->string('secondary_street')->nullable();
            $table->string('number')->nullable();
            $table->string('post_code')->comment('Codigo postal')->nullable();
            $table->foreignId('state_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('pgsql-ignug')->dropIfExists('address');
    }

}
