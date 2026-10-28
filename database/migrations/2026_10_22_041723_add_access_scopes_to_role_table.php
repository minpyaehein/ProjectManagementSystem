<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role', function (Blueprint $table) {
            $table->string('customer_access')
                ->default('assigned')
                ->after('description');

            $table->string('supplier_access')
                ->default('assigned')
                ->after('customer_access');

            $table->string('project_access')
                ->default('assigned')
                ->after('supplier_access');
        });
    }

    public function down(): void
    {
        Schema::table('role', function (Blueprint $table) {
            $table->dropColumn([
                'customer_access',
                'supplier_access',
                'project_access',
            ]);
        });
    }
};
