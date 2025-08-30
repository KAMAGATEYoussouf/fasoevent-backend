<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEventsTableToChangeImageColumn extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->mediumText('image')->change(); // Ou longText() pour des images encore plus grandes
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('image', 255)->change(); // Rétablit l'ancien type si nécessaire
        });
    }
}
