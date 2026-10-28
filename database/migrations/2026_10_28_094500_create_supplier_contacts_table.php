<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('supplier_contacts')) {
            return;
        }

        Schema::create('supplier_contacts', function (Blueprint $table): void {
            $table->id('supplier_contact_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('supplier_id')
                ->on('suppliers')
                ->cascadeOnDelete();

            $table->index(['supplier_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_contacts');
    }
};
