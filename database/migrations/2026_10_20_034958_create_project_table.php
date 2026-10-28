<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project', function (Blueprint $table) {
           $table->id('project_id');
            $table->string('name');
            $table->string('vendor');
            $table->string('accountManagerName');
            $table->string('status');
            $table->date('date');
            $table->string('type');
            $table->string('productCategory');
            $table->decimal('cost', 10, 2)->default(0);
            $table->decimal('revenue', 10, 2)->default(0);
            $table->foreignId('log_id')->constrained('log', 'log_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
