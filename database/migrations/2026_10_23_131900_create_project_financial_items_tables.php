<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_costs', function (Blueprint $table): void {
            $table->id('project_cost_id');
            $table->unsignedBigInteger('project_id');
            $table->date('item_date');
            $table->string('description', 255);
            $table->decimal('amount', 20, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('project_id')
                ->references('project_id')
                ->on('project')
                ->cascadeOnDelete();

            $table->index(['project_id', 'is_active']);
        });

        Schema::create('project_revenues', function (Blueprint $table): void {
            $table->id('project_revenue_id');
            $table->unsignedBigInteger('project_id');
            $table->date('item_date');
            $table->string('description', 255);
            $table->decimal('amount', 20, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('project_id')
                ->references('project_id')
                ->on('project')
                ->cascadeOnDelete();

            $table->index(['project_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_revenues');
        Schema::dropIfExists('project_costs');
    }
};
