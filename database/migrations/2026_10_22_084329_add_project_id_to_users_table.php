<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'project_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedBigInteger('project_id')
                ->nullable()
                ->after('role_id');

            $table->foreign('project_id')
                ->references('project_id')
                ->on('project')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (!Schema::hasColumn('users', 'project_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign([
                'project_id',
            ]);

            $table->dropColumn('project_id');
        });
    }
};
