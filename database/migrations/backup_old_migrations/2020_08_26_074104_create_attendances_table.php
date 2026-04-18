<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id');
            $table->string('entry_ip')->nullable();
            $table->string('entry_location')->nullable();
            $table->string('exit_ip')->nullable();
            $table->string('exit_location')->nullable();
            $table->string('registered')->nullable();
            $table->string('time')->nullable();
            $table->string('entry_status')->nullable();
            $table->string('exit_status')->nullable();
            $table->string('daily_report')->nullable();
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
        Schema::dropIfExists('attendances');
    }
}