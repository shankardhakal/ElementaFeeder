<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'downloading', 'chunking', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('file_path', 2048)->nullable();
            $table->unsignedInteger('total_chunks')->nullable();
            $table->unsignedInteger('processed_chunks')->default(0);
            $table->unsignedInteger('products_processed')->default(0);
            $table->unsignedInteger('products_created')->default(0);
            $table->unsignedInteger('products_updated')->default(0);
            $table->string('processing_batch_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_jobs');
    }
};