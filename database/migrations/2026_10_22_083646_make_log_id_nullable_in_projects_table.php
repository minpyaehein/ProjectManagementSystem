<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project')) {
            return;
        }

        if (!Schema::hasColumn('project', 'log_id')) {
            return;
        }

        Schema::table('project', function (Blueprint $table): void {
            $table->unsignedBigInteger('log_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('project')) {
            return;
        }

        if (!Schema::hasColumn('project', 'log_id')) {
            return;
        }

        Schema::table('project', function (Blueprint $table): void {
            $table->unsignedBigInteger('log_id')
                ->nullable(false)
                ->change();
        });
    }
};
