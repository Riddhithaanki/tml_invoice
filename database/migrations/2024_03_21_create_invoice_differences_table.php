<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_differences', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->json('basic_details_differences')->nullable();
            $table->json('items_differences')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, resolved
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_differences');
    }
};
