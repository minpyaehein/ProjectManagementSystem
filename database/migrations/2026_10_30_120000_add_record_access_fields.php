<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change these names only if your real tables use different names.
     */
    private array $tables = [
        'projects',
        'customers',
        'suppliers',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->index();
                }

                if (! Schema::hasColumn($tableName, 'assigned_user_id')) {
                    $table->unsignedBigInteger('assigned_user_id')->nullable()->index();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'created_by')) {
                    $table->dropColumn('created_by');
                }

                if (Schema::hasColumn($tableName, 'assigned_user_id')) {
                    $table->dropColumn('assigned_user_id');
                }
            });
        }
    }
};
