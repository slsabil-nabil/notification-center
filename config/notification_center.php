<?php

return [

    // موديل المستخدم الذي يستقبل الإشعار
    'user_model' => \App\Models\User::class,

    // من هم الـ admins الافتراضيون؟
    'admin_scope' => [
        'column' => 'is_superadmin',
        'value'  => 1,
    ],

    // اسم جدول المستخدمين (للفورين كي)
    'users_table' => 'users',

];
