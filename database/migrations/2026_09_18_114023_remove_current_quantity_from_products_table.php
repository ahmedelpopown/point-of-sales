<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Retain products.current_quantity temporarily for legacy compatibility and data safety
    }

    public function down(): void
    {
    }
};