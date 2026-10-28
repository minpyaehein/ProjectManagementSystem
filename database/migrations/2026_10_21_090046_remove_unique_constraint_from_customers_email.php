<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // No action required.
        // The customers.email column is already not unique.
    }

    public function down(): void
    {
        // No action required.
    }
};
