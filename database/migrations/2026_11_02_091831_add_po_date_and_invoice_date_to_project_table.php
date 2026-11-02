<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'project',
            function (Blueprint $table): void {
                $table
                    ->date('po_date')
                    ->nullable()
                    ->after('end_date');

                $table
                    ->date('invoice_date')
                    ->nullable()
                    ->after('po_date');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'project',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'po_date',
                    'invoice_date',
                ]);
            }
        );
    }
};
