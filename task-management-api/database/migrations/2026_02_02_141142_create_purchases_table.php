<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_purchases_table.php
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // user_id → اليوزر اللي اشترى

            $table->foreignId('plan_id')->constrained();
            // plan_id → الخطة اللي اشتراها (Pro في حالتنا)

            $table->string('stripe_payment_id')->nullable();
            // stripe_payment_id → معرف الدفعة من Stripe

            $table->decimal('amount', 100, 2);
            // amount → المبلغ المدفوع

            $table->string('currency', 3)->default('USD');
            // currency → العملة (USD, EGP, etc)

            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            // status → حالة الدفعة:
            // pending → في الانتظار
            // completed → تمت بنجاح
            // failed → فشلت
            // refunded → تم الاسترجاع
            
            $table->string('stripe_session_id')->nullable()->after('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
