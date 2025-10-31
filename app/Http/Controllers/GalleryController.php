<?php
// app/Http/Controllers/GalleryController.php
namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class GalleryController extends Controller
{
    // ==================== ADMIN DASHBOARD METHODS ====================

    /**
     * Display the admin gallery management dashboard
     */
public function index()
{
    // SIMPLE: Just get galleries with their images
    $galleries = Gallery::with(['images']) // This loads all images for each gallery
        ->withCount('images') // This counts the images
        ->orderBy('sort_order')
        ->orderBy('created_at', 'desc')
        ->paginate(12);

    $totalGalleries = Gallery::count();
    $activeGalleries = Gallery::where('is_active', true)->count();
    $totalImages = GalleryImage::count();
    $featuredImages = GalleryImage::where('is_featured', true)->where('is_active', true)->count();
    $categories = Gallery::getCategories();

    return view('admin.galleries.index', compact(
        'galleries',
        'totalGalleries',
        'activeGalleries',
        'totalImages',
        'featuredImages',
        'categories'
    ));
}

/**
 * Display a single gallery
 */
public function show($id)
{
    // Get the gallery with its images ordered by sort_order
    $gallery = Gallery::with(['images' => function($query) {
        $query->orderBy('sort_order');
    }])->findOrFail($id);

    return view('admin.galleries.show', compact('gallery'));
}

    /**
     * Show the form for creating a new gallery
     */
    public function create()
    {
        $categories = Gallery::getCategories();
        return view('admin.galleries.form', compact('categories'));
    }

    /**
     * Store a newly created gallery
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Gallery::create($validated);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Gallery created successfully!');
    }

    /**
     * Show the form for editing a gallery
     */
    public function edit(Gallery $gallery)
    {
        $categories = Gallery::getCategories();
        return view('admin.galleries.form', compact('gallery', 'categories'));
    }

    /**
     * Update the specified gallery
     */
    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $gallery->update($validated);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Gallery updated successfully!');
    }

    /**
     * Delete the specified gallery
     */
    public function destroy(Gallery $gallery)
{
    try {
        // Delete associated images first if needed
        $gallery->images()->delete();
        
        // Delete the gallery
        $gallery->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Gallery deleted successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting gallery: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Upload images to gallery
     */
    public function uploadImages(Request $request)
{
    try {
        $request->validate([
            'gallery_id' => 'required|exists:galleries,id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category' => 'nullable|string|max:100',
            'titles' => 'sometimes|array',
            'titles.*' => 'nullable|string|max:255',
            'alt_texts' => 'sometimes|array',
            'alt_texts.*' => 'nullable|string|max:255',
            'active_status' => 'sometimes|array',
            'active_status.*' => 'nullable|boolean'
        ]);

        $gallery = Gallery::findOrFail($request->gallery_id);
        $uploadedImages = [];

        DB::transaction(function () use ($request, $gallery, &$uploadedImages) {
            foreach ($request->file('images') as $index => $image) {
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Store original image
                $imagePath = $image->storeAs('gallery_images', $filename, 'public');

                // Get custom title if provided, otherwise use filename without extension
                $customTitle = $request->titles[$index] ?? null;
                $title = $customTitle ?: pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                
                // Get custom alt text if provided, otherwise use title
                $customAlt = $request->alt_texts[$index] ?? null;
                $altText = $customAlt ?: $title;

                // Get active status (default to true if not provided)
                $isActive = $request->active_status[$index] ?? true;
                $isActive = filter_var($isActive, FILTER_VALIDATE_BOOLEAN);

                // Get next sort order
                $maxSortOrder = GalleryImage::where('gallery_id', $gallery->id)->max('sort_order') ?? 0;

                $galleryImage = GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'title' => $title,
                    'image_path' => $imagePath,
                    'image_alt' => $altText,
                    'category' => $request->category ?? 'any',
                    'sort_order' => $maxSortOrder + 1,
                    'is_active' => $isActive
                ]);

                $uploadedImages[] = $galleryImage;
            }
        });

        return response()->json([
            'message' => count($uploadedImages) . ' images uploaded successfully!',
            'count' => count($uploadedImages)
        ]);

    } catch (\Exception $e) {
        \Log::error('Upload error:', ['error' => $e->getMessage()]);
        
        return response()->json([
            'message' => 'Upload failed: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get all images for AJAX loading
     */
    public function getAllImages()
{
    try {
        $images = GalleryImage::with(['gallery' => function($query) {
            $query->select('id', 'title');
        }])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($image) {
            return [
                'id' => $image->id,
                'title' => $image->title,
                'caption' => $image->caption,
                'image_path' => $image->image_path,
                'image_alt' => $image->image_alt,
                'is_featured' => $image->is_featured,
                'is_active' => $image->is_active,
                'sort_order' => $image->sort_order,
                'category' => $image->category,
                'gallery' => $image->gallery ? [
                    'id' => $image->gallery->id,
                    'title' => $image->gallery->title
                ] : null,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'images' => $images,
            'count' => $images->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load images: ' . $e->getMessage(),
            'images' => []
        ], 500);
    }
}

public function setFeatured($id)
{
    try {
        $image = GalleryImage::findOrFail($id);
            
        // Set this image as featured
        $image->update(['is_featured' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Image set as featured successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error setting featured image: ' . $e->getMessage()
        ], 500);
    }
}

public function removeFeatured($id)
{
    try {
        $image = GalleryImage::findOrFail($id);
        $image->update(['is_featured' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Image removed from featured successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error removing featured image: ' . $e->getMessage()
        ], 500);
    }
}

    /**
 * Get image data for editing
 */
public function getImage($id)
{
    $image = GalleryImage::with('gallery')->findOrFail($id);
    return response()->json(['image' => $image]);
}

/**
 * Show image edit form (if you want a separate page)
 */
public function editImage($id)
{
    $image = GalleryImage::with('gallery')->findOrFail($id);
    $galleries = Gallery::active()->get();
    $categories = GalleryImage::getCategories();
    
    return view('admin.galleries.edit-image', compact('image', 'galleries', 'categories'));
}
    // ==================== API METHODS (Keep your existing API methods) ====================

    /**
     * Create Gallery - Add new galleries with title, description, category
     */
    public function createGallery(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $gallery = Gallery::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category ?? 'any',
            'is_active' => $request->is_active ?? true,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return response()->json([
            'message' => 'Gallery created successfully',
            'gallery' => $gallery
        ], 201);
    }

    /**
     * Read/List Galleries - Display all galleries with pagination/filtering
     */
    public function getGalleries(Request $request)
    {
        $query = Gallery::with(['images'])->withCount(['images', 'activeImages']);

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $galleries = $query->paginate($request->get('per_page', 12));

        return response()->json([
            'galleries' => $galleries,
            'filters' => $request->all()
        ]);
    }

    /**
     * Get single gallery with images
     */
    public function getGallery($id)
    {
        $gallery = Gallery::with(['images' => function($query) {
            $query->ordered();
        }])->findOrFail($id);

        return response()->json(['gallery' => $gallery]);
    }

    /**
     * Update Gallery - Edit gallery details
     */
    public function updateGallery(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $gallery->update($request->all());

        return response()->json([
            'message' => 'Gallery updated successfully',
            'gallery' => $gallery
        ]);
    }

    /**
     * Delete Gallery - Remove galleries (with handling of related images)
     */
    public function deleteGallery($id)
    {
        DB::transaction(function () use ($id) {
            $gallery = Gallery::findOrFail($id);
            
            // Delete associated images
            foreach ($gallery->images as $image) {
                // Delete physical image file
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                // Delete thumbnail if exists
                $thumbnailPath = 'thumbnails/' . basename($image->image_path);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                $image->delete();
            }
            
            $gallery->delete();
        });

        return response()->json(['message' => 'Gallery deleted successfully']);
    }

    /**
     * Reorder Galleries
     */
    public function reorderGalleries(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:galleries,id',
            'order.*.sort_order' => 'required|integer'
        ]);

        foreach ($request->order as $item) {
            Gallery::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Galleries reordered successfully']);
    }

    /**
     * Get gallery categories
     */
    public function getGalleryCategories()
    {
        $categories = Gallery::distinct()
            ->whereNotNull('category')
            ->where('category', '!=', 'any')
            ->pluck('category');

        return response()->json(['categories' => $categories]);
    }

    /**
     * Upload Images - Add multiple images to galleries
     */
    public function uploadImagesApi(Request $request, $galleryId)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category' => 'nullable|string|max:100'
        ]);

        $gallery = Gallery::findOrFail($galleryId);
        $uploadedImages = [];

        DB::transaction(function () use ($request, $gallery, &$uploadedImages) {
            foreach ($request->file('images') as $image) {
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Store original image
                $imagePath = $image->storeAs('gallery_images', $filename, 'public');
                

                // Get next sort order
                $maxSortOrder = GalleryImage::where('gallery_id', $gallery->id)->max('sort_order') ?? 0;

                $galleryImage = GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'title' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME),
                    'image_path' => $imagePath,
                    'image_alt' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME),
                    'category' => $request->category ?? 'any',
                    'sort_order' => $maxSortOrder + 1,
                    'is_active' => true
                ]);

                $uploadedImages[] = $galleryImage;
            }
        });

        return response()->json([
            'message' => count($uploadedImages) . ' images uploaded successfully',
            'images' => $uploadedImages
        ], 201);
    }

    /**
     * Image Listing - Display images within galleries
     */
    public function getImages(Request $request, $galleryId = null)
{
    // Start with active images by default
    $query = GalleryImage::with('gallery')->active();

    // Allow showing inactive images if explicitly requested
    if ($request->has('include_inactive') && $request->boolean('include_inactive')) {
        $query = GalleryImage::with('gallery');
    }

    if ($galleryId) {
        $query->where('gallery_id', $galleryId);
    }

    // Filter by category - use your model scope
    if ($request->has('category') && $request->category !== 'all') {
        $query->byCategory($request->category);
    }

    // Use model's ordered scope instead of manual ordering
    $images = $query->ordered()->paginate($request->get('per_page', 24));

    return response()->json(['images' => $images]);
}



    /**
 * Edit Image Details - Update title, alt text, caption, category
 */
public function updateImage(Request $request, $id)
{
    $image = GalleryImage::findOrFail($id);

    $request->validate([
        'title' => 'nullable|string|max:255',
        'image_alt' => 'nullable|string|max:255',
        'caption' => 'nullable|string',
        'category' => 'nullable|string|max:100',
        'sort_order' => 'nullable|integer',
        'is_featured' => 'nullable|boolean',
        'is_active' => 'nullable|boolean'
    ]);

    // Log the incoming data for debugging
    \Log::info('Updating image data:', [
        'id' => $id,
        'data' => $request->all(),
        'is_featured' => $request->is_featured,
        'is_active' => $request->is_active
    ]);

    // Handle checkbox values - ensure they are proper booleans
    $data = $request->all();
    
    // Convert checkbox values to proper booleans
    $data['is_featured'] = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
    
    // Ensure sort_order is integer
    $data['sort_order'] = intval($data['sort_order'] ?? 0);

    $image->update($data);

    return response()->json([
        'message' => 'Image updated successfully',
        'image' => $image->fresh()
    ]);
}

    /**
     * Delete Images - Remove individual images
     */
    public function deleteImage($id)
    {
        $image = GalleryImage::findOrFail($id);

        // Delete physical files
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete thumbnail
        $thumbnailPath = 'thumbnails/' . basename($image->image_path);
        if (Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('public')->delete($thumbnailPath);
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    /**
     * Bulk Operations - Move multiple images between galleries/categories
     */
    public function bulkImageOperations(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:gallery_images,id',
            'action' => 'required|in:move_gallery,change_category,delete,activate,deactivate,set_featured'
        ]);

        $imageIds = $request->image_ids;

        switch ($request->action) {
            case 'move_gallery':
                $request->validate(['gallery_id' => 'required|exists:galleries,id']);
                GalleryImage::whereIn('id', $imageIds)->update(['gallery_id' => $request->gallery_id]);
                break;

            case 'change_category':
                $request->validate(['category' => 'required|string|max:100']);
                GalleryImage::whereIn('id', $imageIds)->update(['category' => $request->category]);
                break;

            case 'delete':
                $images = GalleryImage::whereIn('id', $imageIds)->get();
                foreach ($images as $image) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }
                break;

            case 'activate':
                GalleryImage::whereIn('id', $imageIds)->update(['is_active' => true]);
                break;

            case 'deactivate':
                GalleryImage::whereIn('id', $imageIds)->update(['is_active' => false]);
                break;

            case 'set_featured':
                GalleryImage::whereIn('id', $imageIds)->update(['is_featured' => true]);
                break;

            case 'remove_featured':
                    // Remove featured status from ALL selected images
                    GalleryImage::whereIn('id', $imageIds)->update(['is_featured' => false]);
                    break;

        }

        return response()->json(['message' => 'Bulk operation completed successfully']);
    }

    // ==================== PUBLIC GALLERY METHODS ====================

    /**
     * Display public gallery page
     */
    public function publicGallery()
{
    $galleries = Gallery::with(['activeImages' => function($query) {
        $query->ordered(); // Use model scope
    }])
    ->active() // Use model scope instead of manual where
    ->ordered() // Use model scope for consistent ordering
    ->get();
    
    $categories = Gallery::active() // Use model scope
        ->distinct()
        ->pluck('category')
        ->filter()
        ->values();
    
    return view('gallery', compact('galleries', 'categories'));
}

    /**
     * Display a single gallery with its images (public)
     */
    public function showGallery($id)
{
    $gallery = Gallery::with(['activeImages' => function($query) {
            $query->ordered(); // Use model scope
        }])
        ->active() // Use model scope
        ->findOrFail($id);

    $totalImages = GalleryImage::active()->count(); // Use model scope

    return view('gallery-show', compact('gallery', 'totalImages'));
}

    /**
     * API endpoint to get featured images (for your landing page)
     */
    public function getFeaturedImages()
    {
        $featuredImages = GalleryImage::with('gallery')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $hasEnoughImages = $featuredImages->count() >= 8;
        $totalImages = GalleryImage::where('is_active', true)->count();

        return response()->json([
            'featuredImages' => $featuredImages,
            'hasEnoughImages' => $hasEnoughImages,
            'totalImages' => $totalImages
        ]);
    }

    // In your GalleryController

  public function bulkDelete(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:gallery_images,id',
            'gallery_id' => 'required|exists:galleries,id'
        ]);

        try {
            $imageIds = $request->image_ids;
            $deletedCount = GalleryImage::whereIn('id', $imageIds)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} images deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting images: ' . $e->getMessage()
            ], 500);
        }
    }

public function reorder(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:gallery_images,id',
            'updates.*.sort_order' => 'required|integer'
        ]);

        try {
            \DB::beginTransaction();
            
            foreach ($request->updates as $update) {
                GalleryImage::where('id', $update['id'])->update([
                    'sort_order' => $update['sort_order']
                ]);
            }
            
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image order updated successfully'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating image order: ' . $e->getMessage()
            ], 500);
        }
    }

    
    // ==================== UTILITY METHODS ====================

    /**
     * Get gallery statistics
     */
    public function getStatistics()
    {
        $totalGalleries = Gallery::count();
        $activeGalleries = Gallery::active()->count();
        $totalImages = GalleryImage::count();
        $activeImages = GalleryImage::active()->count();
        $featuredImages = GalleryImage::featured()->count();
        $categories = Gallery::distinct('category')->pluck('category');

        return response()->json([
            'total_galleries' => $totalGalleries,
            'active_galleries' => $activeGalleries,
            'total_images' => $totalImages,
            'active_images' => $activeImages,
            'featured_images' => $featuredImages,
            'categories' => $categories
        ]);
    }

    
}