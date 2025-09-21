<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\BaseModelSoftDeleteDefault;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use BaseModelSoftDeleteDefault;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('order');
            $table->foreignId('product_id')->constrained('product');
            $table->integer('quantity')->default(0);
            $table->double('subtotal')->default(0);
            $table->text('note')->nullable();
            $this->base($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_detail');
    }
};
