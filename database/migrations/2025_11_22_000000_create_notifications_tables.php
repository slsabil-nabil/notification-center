<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) جدول notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // أعمدة اختيارية تربط الإشعار بسياق خارجي (وكالة، طلب، ...إلخ)
            // احذفها لو ما تحتاجها الآن
            $table->unsignedBigInteger('business_id')->nullable();
            $table->unsignedBigInteger('application_id')->nullable();

            $table->string('category');           // نوع الإشعار (new_application, booking_created, ...)
            $table->string('title');              // عنوان افتراضي
            $table->text('body');                 // النص الافتراضي
            $table->json('data')->nullable();     // بيانات إضافية (روابط، i18n, ...)
            $table->string('dedupe_key')->nullable();     // لمنع التكرار إن احتجت
            $table->boolean('requires_action')->default(false);
            $table->timestamps();
        });

        // 2) جدول notification_recipients
        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('notification_id')
                ->constrained('notifications')
                ->onDelete('cascade');

            // نفترض اسم الجدول users و الـ PK هو id
            // لو اختلف عندك، نضبطه لاحقًا من config الباكج
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

        // FK على users (محطوطة لوحدها عشان لو حبيت تغيّرها لاحقاً من config)
        Schema::table('notification_recipients', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')        // غيّرها لو جدول المستخدمين عندك باسم مختلف
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notification_recipients', function (Blueprint $table) {
            // إزالة الـ FK قبل الحذف احتياطاً
            $table->dropForeign(['user_id']);
            $table->dropUnique('recipients_notification_user_unique');
        });

        Schema::dropIfExists('notification_recipients');
        Schema::dropIfExists('notifications');
    }
};
