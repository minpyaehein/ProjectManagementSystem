<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_documents', function (Blueprint $table): void {
            $table->id('project_document_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('original_name', 255);
            $table->string('stored_name', 255);
            $table->string('storage_path', 500);
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->foreign('project_id')
                ->references('project_id')->on('project')->cascadeOnDelete();
            $table->foreign('uploaded_by')
                ->references('user_id')->on('users')->nullOnDelete();
            $table->index(['project_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
