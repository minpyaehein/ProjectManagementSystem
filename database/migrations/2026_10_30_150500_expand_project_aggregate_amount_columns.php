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
                    ->decimal('cost', 20, 2)
                    ->default(0)
                    ->change();

                $table
                    ->decimal('revenue', 20, 2)
                    ->default(0)
                    ->change();
            }
        );
    }

    public function down(): void
    {
        /*
         * Do not automatically shrink these columns.
         * Existing values may exceed the old precision.
         */
    }
};
