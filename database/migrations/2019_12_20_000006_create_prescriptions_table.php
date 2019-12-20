<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('patient_name')->nullable();

            $table->string('email')->nullable();

            $table->longText('disease')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }
}
