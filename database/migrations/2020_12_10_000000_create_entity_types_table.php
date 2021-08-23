<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('entity_types')) {
            return;
        }

        Schema::create('entity_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('title');
            $table->string('type');
            $table->string('built_in_type')->nullable();
            $table->json('entity_configuration')->nullable();
            $table->unsignedInteger('default_form_configuration_id')->nullable();
            $table->timestamps();
            if(config('laravel-vue-forms.entities_soft_delete')) {
                $table->softDeletes();
            }

            $table->foreign('default_form_configuration_id')
                ->references('id')
                ->on('form_configurations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_types');
    }
}
