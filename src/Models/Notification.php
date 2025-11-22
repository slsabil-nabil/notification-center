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

    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function getTitleLocalizedAttribute(): string
    {
        // لو العنوان نفسه Array => إذًا هو multilingual كما خزّنته أنت
        if (is_array($this->title)) {
            $loc = app()->getLocale();
            return $this->title[$loc] ?? $this->title['en'] ?? reset($this->title);
        }

        // لو ما هو Array نخليه كما هو
        return (string) $this->title;
    }

    public function getBodyLocalizedAttribute(): string
    {
        if (is_array($this->body)) {
            $loc = app()->getLocale();
            return $this->body[$loc] ?? $this->body['en'] ?? reset($this->body);
        }

        return (string) $this->body;
    }

    public static function existsByDedupeKey(string $dedupeKey): bool
    {
        return static::where('dedupe_key', $dedupeKey)->exists();
    } 

    public function getActionUrlAttribute(): ?string
    {
        // إذا كان action_url مخزنا في data
        if (is_array($this->data) && isset($this->data['action_url'])) {
            return $this->data['action_url'];
        }
        
        // إذا كان data نص JSON
        if (is_string($this->data)) {
            $decoded = json_decode($this->data, true);
            if (isset($decoded['action_url'])) {
                return $decoded['action_url'];
            }
        }
        
        return null;
    }
}