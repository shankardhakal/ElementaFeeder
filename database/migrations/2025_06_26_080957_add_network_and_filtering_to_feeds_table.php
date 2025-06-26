<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->foreignId('network_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->json('filtering_rules')->nullable()->after('parser_options');
        });
    }

    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropForeign(['network_id']);
            $table->dropColumn('network_id');
            $table->dropColumn('filtering_rules');
        });
    }
};