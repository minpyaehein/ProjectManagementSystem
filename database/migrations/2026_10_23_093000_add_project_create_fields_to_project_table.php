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

        Schema::table('project', function (Blueprint $table): void {
            if (!Schema::hasColumn('project', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable();
            }

            if (!Schema::hasColumn('project', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable();
            }

            if (!Schema::hasColumn('project', 'end_date')) {
                $table->date('end_date')->nullable();
            }

            if (!Schema::hasColumn('project', 'remark')) {
                $table->text('remark')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('project')) {
            return;
        }

        Schema::table('project', function (Blueprint $table): void {
            $columns = [];

            foreach (['customer_id', 'supplier_id', 'end_date', 'remark'] as $column) {
                if (Schema::hasColumn('project', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
