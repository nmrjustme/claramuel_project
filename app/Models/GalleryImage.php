<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gallery_id',
        'title',
        'image_path',
        'image_alt',
        'caption',
        'category',
        'sort_order',
        'is_featured',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
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
        'is_featured' => false,
        'is_active' => true,
        'sort_order' => 0,
        'category' => 'any',
    ];

    /**
     * Get the gallery that owns the image.
     */
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }
    

    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : '';
    }

    /**
     * Get the image dimensions (placeholder).
     */
    public function getDimensionsAttribute(): string
    {
        return '1920x1080'; // Placeholder - you can implement actual dimensions later
    }

    /**
     * Get file size (if file exists).
     */
    public function getFileSizeAttribute(): string
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            $size = Storage::disk('public')->size($this->image_path);
            return $this->formatBytes($size);
        }
        return 'Unknown';
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Scope a query to only include active images.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured images.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
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
     * Scope a query to filter by gallery.
     */
    public function scopeByGallery($query, $galleryId)
    {
        if ($galleryId) {
            return $query->where('gallery_id', $galleryId);
        }
        return $query;
    }

    /**
     * Check if image is the only featured image in its gallery.
     */
    public function getIsOnlyFeaturedAttribute(): bool
    {
        if (!$this->is_featured) {
            return false;
        }

        return !$this->gallery->featuredImages()
            ->where('id', '!=', $this->id)
            ->exists();
    }

    /**
     * Get all unique categories from images.
     */
    public static function getCategories(): array
    {
        return self::distinct()
            ->whereNotNull('category')
            ->where('category', '!=', 'any')
            ->pluck('category')
            ->toArray();
    }

    /**
     * Boot method for handling model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Set default sort order before creating
        static::creating(function ($image) {
            if (is_null($image->sort_order)) {
                $maxOrder = static::where('gallery_id', $image->gallery_id)->max('sort_order');
                $image->sort_order = $maxOrder ? $maxOrder + 1 : 0;
            }
        });

        // Delete image file when model is deleted
        static::deleting(function ($image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        });
    }
}