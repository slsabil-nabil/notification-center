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

    protected $casts = [
        'data'            => 'array',
        'requires_action' => 'boolean',
    ];

    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function getTitleLocalizedAttribute(): string
    {
        $loc = app()->getLocale();

        return data_get($this->data, "i18n.$loc.title")
            ?? data_get($this->data, "i18n.en.title")
            ?? (string) $this->title
            ?? __('Notification');
    }

    public function getBodyLocalizedAttribute(): string
    {
        $loc = app()->getLocale();

        return data_get($this->data, "i18n.$loc.body")
            ?? data_get($this->data, "i18n.en.body")
            ?? (string) $this->body
            ?? '';
    }

    public static function existsByDedupeKey(string $dedupeKey): bool
    {
        return static::where('dedupe_key', $dedupeKey)->exists();
    }

}
