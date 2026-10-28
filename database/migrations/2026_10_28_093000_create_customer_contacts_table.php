<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_contacts')) {
            return;
        }

        Schema::create('customer_contacts', function (Blueprint $table): void {
            $table->id('customer_contact_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->cascadeOnDelete();

            $table->index(['customer_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_contacts');
    }
};
