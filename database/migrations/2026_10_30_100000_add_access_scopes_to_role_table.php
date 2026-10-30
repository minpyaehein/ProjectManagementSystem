<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role', function (Blueprint $table): void {
            if (! Schema::hasColumn('role', 'customer_access_scope')) {
                $table->string('customer_access_scope', 20)->default('assigned');
            }
            if (! Schema::hasColumn('role', 'supplier_access_scope')) {
                $table->string('supplier_access_scope', 20)->default('assigned');
            }
            if (! Schema::hasColumn('role', 'project_access_scope')) {
                $table->string('project_access_scope', 20)->default('assigned');
            }
            if (! Schema::hasColumn('role', 'status')) {
                $table->string('status', 20)->default('Active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('role', function (Blueprint $table): void {
            foreach (['customer_access_scope','supplier_access_scope','project_access_scope'] as $column) {
                if (Schema::hasColumn('role', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
