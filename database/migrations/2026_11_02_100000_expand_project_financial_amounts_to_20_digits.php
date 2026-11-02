<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project', function (Blueprint $table): void {
            $table->decimal('cost', 22, 2)->default(0)->change();
            $table->decimal('revenue', 22, 2)->default(0)->change();
        });

        Schema::table('project_costs', function (Blueprint $table): void {
            $table->decimal('amount', 22, 2)->change();
        });

        Schema::table('project_revenues', function (Blueprint $table): void {
            $table->decimal('amount', 22, 2)->change();
        });
    }

    public function down(): void
    {
        // Intentionally empty. Shrinking could fail after storing large values.
    }
};
