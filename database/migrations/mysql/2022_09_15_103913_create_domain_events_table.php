<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('domain_events', function (Blueprint $table) {
            $table->char('id', 36)->unique()->primary();
            $table->char('aggregate_id', 36);
            $table->string('name', 255);
            $table->json('body');
            $table->timestamp('occurred_at', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_events');
    }
};
