<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFormConfigurationsForEntityTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_configurations', function (Blueprint $table) {
            $table->unsignedInteger('entity_type_id')->nullable()->after('active');
            $table->foreign('entity_type_id')
                ->references('id')
                ->on('entity_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_configurations', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['entity_type_id']);
            }
            $table->dropColumn('entity_type_id');
        });
    }
}
