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
        'title' => 'array',
        'body' => 'array',
        'data' => 'array',
        'requires_action' => 'boolean',
    ];

    public function getTitleLocalizedAttribute(): string
    {
        $loc = app()->getLocale();
        return $this->title[$loc] ?? $this->title['en'] ?? reset($this->title) ?? '';
    }

    public function getBodyLocalizedAttribute(): string
    {
        $loc = app()->getLocale();
        return $this->body[$loc] ?? $this->body['en'] ?? reset($this->body) ?? '';
    }

    public function getActionUrlAttribute(): ?string
    {
        return $this->data['action_url'] ?? null;
    }
}
