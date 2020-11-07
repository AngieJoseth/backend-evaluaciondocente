<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkdaysTable extends Migration
{
    public function up()
    {
        Schema::connection('pgsql-attendance')->create('workdays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id');
            $table->string('description', 300)->nullable();
            $table->json('observations')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('duration')->nullable();
            $table->foreignId('type_id')->constrained('ignug.catalogues');
            $table->foreignId('state_id')->constrained('ignug.states');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('pgsql-ignug')->dropIfExists('workdays');
    }
}
