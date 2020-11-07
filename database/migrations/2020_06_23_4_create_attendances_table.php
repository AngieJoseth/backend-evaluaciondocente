<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::connection('pgsql-attendance')->create('attendances', function (Blueprint $table) {
            $table->id();
            $table->morphs('attendanceable');
            $table->date('date');
            $table->foreignId('type_id')->constrained('ignug.catalogues');
            $table->foreignId('state_id')->constrained('ignug.states');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('pgsql-attendance')->dropIfExists('attendances');
    }
}
