<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'leads', function (Blueprint $table) {
                $table->increments('id');
                $table->string('incoming_number');
                $table->string('city');
                $table->string('state');
                $table->string('caller_name');
                $table->timestamps();

                $table->integer('lead_source_id');
                $table->foreign('lead_source_id')
                    ->references('id')
                    ->on('lead_sources');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('leads');
    }
}
