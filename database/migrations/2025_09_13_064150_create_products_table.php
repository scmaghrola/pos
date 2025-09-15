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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');                     // product title
            $table->unsignedBigInteger('category_id');   // category relation
            $table->decimal('price', 10, 2);             // main price
            $table->decimal('compare_price', 10, 2)->nullable(); // optional compare price
            $table->string('image')->nullable();         // product image path
            $table->string('sku')->nullable();           // stock keeping unit
            $table->decimal('weight', 8, 2)->nullable(); // weight of product
            $table->timestamps();

            // foreign key constraint
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade'); // delete product if category deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
