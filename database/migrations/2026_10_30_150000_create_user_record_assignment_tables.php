<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_user_assignments', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');
            $table->timestamps();
            $table->primary(['user_id', 'project_id']);
            $table->index('project_id');
        });

        Schema::create('customer_user_assignments', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->timestamps();
            $table->primary(['user_id', 'customer_id']);
            $table->index('customer_id');
        });

        Schema::create('supplier_user_assignments', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('supplier_id');
            $table->timestamps();
            $table->primary(['user_id', 'supplier_id']);
            $table->index('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_user_assignments');
        Schema::dropIfExists('customer_user_assignments');
        Schema::dropIfExists('project_user_assignments');
    }
};
