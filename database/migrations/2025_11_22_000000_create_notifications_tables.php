<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         |--------------------------------------------------------------
         | جدول الإشعارات notifications
         |--------------------------------------------------------------
         */
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // ربط اختياري بسياق خارجي (شركة / طلب ...)
            $table->unsignedBigInteger('business_id')->nullable();
            $table->unsignedBigInteger('application_id')->nullable();

            $table->string('category');      // مثل: new_application, booking_created, ...

            // حقول متعددة اللغات
            $table->json('title');           // مثال: {"ar": "...", "en": "..."}
            $table->json('body');            // مثال: {"ar": "...", "en": "..."}

            // بيانات إضافية (action_url, meta ...)
            $table->json('data')->nullable(); // مثال: {"action_url": "https://..."}

            $table->string('dedupe_key')->nullable(); // لمنع التكرار لو احتجت
            $table->boolean('requires_action')->default(false);

            $table->timestamps();
        });

        /*
         |--------------------------------------------------------------
         | جدول مستلمي الإشعارات notification_recipients
         |--------------------------------------------------------------
         */
        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();

            // FK على notifications
            $table->foreignId('notification_id')
                ->constrained('notifications')
                ->onDelete('cascade');

            // نفترض جدول المستخدمين باسم users والـ PK هو id
            $table->unsignedBigInteger('user_id');

            $table->timestamp('read_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->boolean('delivery_error')->default(false);

            $table->timestamps();

            // منع تكرار نفس الإشعار لنفس المستخدم
            $table->unique(
                ['notification_id', 'user_id'],
                'recipients_notification_user_unique'
            );
        });

        // إضافة FK على users في خطوة منفصلة
        Schema::table('notification_recipients', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')   // غيّر الاسم لو جدول المستخدمين مختلف
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // حذف علاقات الـ FK والـ unique أولاً ثم الجداول
        if (Schema::hasTable('notification_recipients')) {
            Schema::table('notification_recipients', function (Blueprint $table) {
                // هذه هي الأسماء الافتراضية للـ FK الناتجة من الـ up()
                $table->dropForeign(['notification_id']);
                $table->dropForeign(['user_id']);

                // حذف الـ unique index
                $table->dropUnique('recipients_notification_user_unique');
            });

            Schema::dropIfExists('notification_recipients');
        }

        Schema::dropIfExists('notifications');
    }
};
