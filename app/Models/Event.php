<?php

namespace App\Models;

use App\Models\Scopes\OrganizerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', // Pastikan category_id ada di fillable
        'organizer_id',
        'title',
        'slug',
        'description',
        'poster_path',
        'date',
        'location',
        'price',
        'stock',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizerScope);
    }

    /**
     * Relasi ke Category (Satu event memiliki satu kategori)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi ke User (Organizer)
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'event_id')->latest();
    }
}