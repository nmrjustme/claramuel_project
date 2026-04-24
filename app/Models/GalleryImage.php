<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GalleryImage extends Model
{
    use HasFactory;

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

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'is_featured' => false,
        'is_active' => true,
        'sort_order' => 0,
        'category' => 'any',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getImageUrlAttribute(): string
    {
        // REFACTORED: Removed 'storage/' prefix
        return $this->image_path ? asset($this->image_path) : '';
    }

    public function getDimensionsAttribute(): string
    {
        return '1920x1080';
    }

    public function getFileSizeAttribute(): string
    {
        // REFACTORED: Use native PHP filesize instead of Laravel Storage
        if ($this->image_path && file_exists(public_path($this->image_path))) {
            $size = filesize(public_path($this->image_path));
            return $this->formatBytes($size);
        }
        return 'Unknown';
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeFeatured($query) { return $query->where('is_featured', true)->where('is_active', true); }
    public function scopeOrdered($query) { return $query->orderBy('sort_order')->orderBy('created_at', 'desc'); }

    public function scopeByCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeByGallery($query, $galleryId)
    {
        if ($galleryId) {
            return $query->where('gallery_id', $galleryId);
        }
        return $query;
    }

    public function getIsOnlyFeaturedAttribute(): bool
    {
        if (!$this->is_featured) return false;
        return !$this->gallery->featuredImages()->where('id', '!=', $this->id)->exists();
    }

    public static function getCategories(): array
    {
        return self::distinct()
            ->whereNotNull('category')
            ->where('category', '!=', 'any')
            ->pluck('category')
            ->toArray();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (is_null($image->sort_order)) {
                $maxOrder = static::where('gallery_id', $image->gallery_id)->max('sort_order');
                $image->sort_order = $maxOrder ? $maxOrder + 1 : 0;
            }
        });

        // REFACTORED: Delete physical file using native PHP unlink
        static::deleting(function ($image) {
            if ($image->image_path && file_exists(public_path($image->image_path))) {
                unlink(public_path($image->image_path));
            }
        });
    }
}