<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gallery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'is_active',
        'sort_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
        'category' => 'any',
    ];

    /**
     * Get the images for the gallery.
     */
    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class);
    }

    /**
     * Get only active images for the gallery.
     */
    public function activeImages(): HasMany
    {
        return $this->images()->where('is_active', true);
    }

    /**
     * Get featured images for the gallery.
     */
    public function featuredImages(): HasMany
    {
        return $this->images()->where('is_featured', true)->where('is_active', true);
    }

    /**
     * Get thumbnail URL for the gallery.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $firstImage = $this->firstImage();
        return $firstImage ? asset('storage/' . $firstImage->image_path) : null;
    }

    /**
     * Get thumbnail alt text for the gallery.
     */
    public function getThumbnailAltAttribute(): ?string
    {
        $firstImage = $this->firstImage();
        return $firstImage ? ($firstImage->image_alt ?? $firstImage->title) : 'No image';
    }

    /**
     * Scope a query to only include active galleries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }
    
/**
 * Get the first uploaded (or first ordered) image in this gallery.
 */
public function firstImage()
{
    return $this->images()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('created_at')
        ->first();
}


    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Get the image count for the gallery.
     */
    public function getImageCountAttribute(): int
    {
        return $this->images()->count();
    }

    /**
     * Get the active image count for the gallery.
     */
    public function getActiveImageCountAttribute(): int
    {
        return $this->activeImages()->count();
    }

    /**
     * Check if gallery has images.
     */
    public function getHasImagesAttribute(): bool
    {
        return $this->images()->exists();
    }

    /**
     * Get all unique categories from galleries.
     */
    public static function getCategories(): array
    {
        return self::distinct()
            ->whereNotNull('category')
            ->where('category', '!=', 'any')
            ->pluck('category')
            ->toArray();
    }
}