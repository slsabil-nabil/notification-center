<?php

namespace Slsabil\NotificationCenter\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'business_id',
        'application_id',
        'category',
        'title',
        'body',
        'data',
        'dedupe_key',
        'requires_action',
    ];

    // فقط requires_action كمتحول منطقي
    protected $casts = [
        'requires_action' => 'boolean',
    ];

    /*
     |--------------------------------------------------------------------------
     | Helpers: فك JSON المخزَّن في الأعمدة
     |--------------------------------------------------------------------------
     */

    protected function decodeColumnToArray(string $column): array
    {
        $raw = $this->attributes[$column] ?? null;

        if (!is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /*
     |--------------------------------------------------------------------------
     | Accessors: النص بحسب لغة الواجهة + رابط الإجراء
     |--------------------------------------------------------------------------
     */

    public function getTitleLocalizedAttribute(): string
    {
        $loc = app()->getLocale(); // 'ar' أو 'en'

        $arr = $this->decodeColumnToArray('title');

        // اختر النص حسب اللغة ثم en ثم ar
        return $arr[$loc]
            ?? ($arr['en'] ?? ($arr['ar'] ?? ''));
    }

    public function getBodyLocalizedAttribute(): string
    {
        $loc = app()->getLocale();

        $arr = $this->decodeColumnToArray('body');

        return $arr[$loc]
            ?? ($arr['en'] ?? ($arr['ar'] ?? ''));
    }

    public function getActionUrlAttribute(): ?string
    {
        $arr = $this->decodeColumnToArray('data');

        return $arr['action_url'] ?? null;
    }

    /*
     |--------------------------------------------------------------------------
     | العلاقات
     |--------------------------------------------------------------------------
     */

    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }
}
