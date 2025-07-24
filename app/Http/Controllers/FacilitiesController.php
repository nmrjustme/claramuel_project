<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\FacilityImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\FacilityDiscount;
use DateTime;

class FacilitiesController extends Controller
{
    protected $facilities;
    protected $roomFacilities;
    protected $privateVilla;

    public function __construct()
    {
        $this->facilities = Facility::with('images')->whereIn('category', ['Pool', 'Park'])->get();
        $this->roomFacilities = Facility::with('images')
            ->whereIn('category', ['Village', 'Room'])
            ->get();

        $this->privateVilla = Facility::with('images')->where('category', 'Village')->get();
    }

    public function showData()
    {
        if (auth()->check() && auth()->user()->role === 'Admin') {
            
            return redirect()->route('admin.dashboard');
            
        }
    
        return view('welcome', [
                'facilities' => $this->facilities,
                'roomFacilities' => $this->roomFacilities,
                'privateVilla' => $this->privateVilla,
        ]);
    }
    
    public function getAvailableRooms(Request $request)
    {
        try {
            $validated = $request->validate([
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in'
            ]);
            
            // Get rooms that are not booked for the given dates
            $bookedRoomIds = Facility::where(function($query) use ($validated) {
                $query->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                      ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('check_in', '<=', $validated['check_in'])
                            ->where('check_out', '>=', $validated['check_out']);
                      });
                })
                ->pluck('room_id');
            
            $availableRooms = Room::whereNotIn('id', $bookedRoomIds)
                                ->where('status', 'available')
                                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $availableRooms
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function AdminIndex()
    {
        $facilities = Facility::with(['images', 'discounts'])
        ->where('category', '!=', 'Cottage')
        ->orderBy('id', 'desc')
        ->get();

        return view('admin.facilities.index', ['facilities' => $facilities]);
    }
    
    public function AdminStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'pax' => 'nullable|integer|min:1',
            'bed_number' => 'nullable|integer|min:1',
            'room_number' => 'nullable|integer|min:1',
            'included' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
    
        // Create the facility excluding the images
        $facility = Facility::create(collect($validatedData)->except('images')->toArray());
    
        // If images are uploaded, process and store them
        if ($request->hasFile('images')) {
            $this->processImages($request->file('images'), $facility->id);
        }
    
        // Return success response with related images
        return response()->json([
            'success' => true,
            'message' => 'Facility created successfully',
            'facility' => $facility->load('images')
        ]);
    }

    public function UpdateFacility(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'pax' => 'nullable|integer|min:1',
                'bed_number' => 'nullable|integer|min:1',
                'room_number' => 'nullable|integer|min:1',
                'included' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new HttpResponseException(
                response()->json(['errors' => $e->errors()], 422)
            );
        }
    
        $facility = Facility::findOrFail($id);
    
        $facility->update(collect($validatedData)->except('images')->toArray());
    
        if ($request->hasFile('images')) {
            $this->processImages($request->file('images'), $facility->id);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Facility updated successfully',
            'facility' => $facility->load('images'),
        ]);
    }

    public function DeleteFacility($id)
    {
        $facility = Facility::findOrFail($id);
        
        foreach ($facility->images as $image) {
            $imagePath = public_path('imgs/facility_img/' . $image->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $image->delete();
        }
        
        $facility->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Facility deleted successfully'
        ]);
    }
    
    public function deleteImage($id)
    {
        // Validate that the ID is a valid numeric value
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:image_fac,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image ID.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Proceed with deletion
        $image = FacilityImage::findOrFail($id);
        $imagePath = public_path('imgs/facility_img/' . $image->image);
    
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }
    
        $image->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
        ]);
    }
    
    public function edit($id)
    {
        try {
            $facility = Facility::findOrFail($id);
    
            return response()->json([
                'success' => true,
                'facility' => $facility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Facility not found or error occurred'
            ], 404);
        }
    }

    public function getImages($id)
    {
        try {
            $facility = Facility::with('images')->find($id);
            
            if (!$facility) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facility not found'
                ], 404);
            }
    
            // Transform images collection
            $transformedImages = $facility->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('imgs/facility_img/' . $image->image),
                    'image' => $image->image // include the original image filename
                ];
            });
    
            return response()->json([
                'success' => true,
                'facility' => [
                    'id' => $facility->id,
                    'name' => $facility->name
                ],
                'images' => $transformedImages
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Failed to fetch facility images: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }
    
    protected function processImages($images, $facilityId)
    {
        try {
            foreach ($images as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('imgs/facility_img'), $imageName);
                
                FacilityImage::create([
                    'fac_id' => $facilityId,
                    'image' => $imageName,
                    'order' => FacilityImage::where('fac_id', $facilityId)->count() + 1
                ]);
            }
        } catch (\Exception $e) {
            throw new \Exception("Failed to process images: " . $e->getMessage());
        }
    }
        
    // Discounts
    
    public function getDiscounts($facilityId)
    {
        $discounts = FacilityDiscount::where('facility_id', $facilityId)
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json([
            'success' => true,
            'discounts' => $discounts
        ]);
    }

    public function addDiscount(Request $request, $facilityId)
    {
        $validatedData = $request->validate([
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Percentage discount cannot be greater than 100%');
                    }
                }
            ],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($facilityId, $request) {
                    $this->validateDiscountDates($facilityId, $value, $request->end_date, null, $fail);
                }
            ],
            'end_date' => 'required|date|after:start_date',
        ]);
    
        $discount = FacilityDiscount::create([
            'facility_id' => $facilityId,
            'discount_type' => $validatedData['discount_type'],
            'discount_value' => $validatedData['discount_value'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Discount added successfully',
            'discount' => $discount
        ]);
    }
    
    public function updateDiscount(Request $request, $discountId)
    {
        $discount = FacilityDiscount::findOrFail($discountId);
        $isCurrentlyActive = now()->between($discount->start_date, $discount->end_date);
    
        $validatedData = $request->validate([
            // ... (existing validation rules)
        ]);
    
        // Additional validation for active discounts
        if ($isCurrentlyActive) {
            // Prevent changing discount type for active discounts
            if ($request->discount_type != $discount->discount_type) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot change discount type while discount is active'
                ], 422);
            }
    
            // Prevent reducing discount value for active discounts
            if ($request->discount_value < $discount->discount_value) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reduce discount value while discount is active'
                ], 422);
            }
        }
    
        $discount->update($validatedData);
    
        return response()->json([
            'success' => true,
            'message' => 'Discount updated successfully',
            'discount' => $discount
        ]);
    }
    
    public function deleteDiscount($discountId)
    {
        $discount = FacilityDiscount::findOrFail($discountId);
        
        // Check if discount is currently active
        $now = now();
        if ($now >= $discount->start_date && $now <= $discount->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete an active discount. Please wait until it expires or edit the dates first.'
            ], 422);
        }
    
        $discount->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Discount deleted successfully'
        ]);
    }
    
    /**
     * Helper method to validate discount dates against existing discounts
     */
    protected function validateDiscountDates($facilityId, $startDate, $endDate, $excludeDiscountId, $fail)
    {
        $query = FacilityDiscount::where('facility_id', $facilityId)
            ->where(function($q) use ($startDate, $endDate) {
                // Check for overlapping date ranges
                $q->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate]);
                })
                // Or check if existing range completely contains new range
                ->orWhere(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
                });
            });
    
        if ($excludeDiscountId) {
            $query->where('id', '!=', $excludeDiscountId);
        }
    
        if ($query->exists()) {
            $fail('This discount overlaps with an existing discount for this facility');
        }
    }
    
    // Get all cottages with pagination and images
    public function getCottage(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $cottages = Facility::with('images')
                    ->where('category', 'cottage')
                    ->paginate($perPage);
        
        // Transform the data to include full image URLs
        $transformed = $cottages->getCollection()->map(function($cottage) {
            $cottage->images->transform(function($image) {
                $image->url = asset('imgs/facility_img/' . $image->image);
                return $image;
            });
            return $cottage;
        });
        
        $cottages->setCollection($transformed);
        
        return response()->json([
            'success' => true,
            'data' => $cottages->items(),
            'current_page' => $cottages->currentPage(),
            'last_page' => $cottages->lastPage(),
            'total' => $cottages->total(),
            'per_page' => $cottages->perPage(),
        ]);
    }
    
    // Store a new cottage with images
    public function storeCottage(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
    
        // Create the cottage
        $cottage = Facility::create([
            'name' => $validatedData['name'],
            'quantity' => $validatedData['quantity'],
            'price' => $validatedData['price'],
            'description' => $validatedData['description'] ?? null,
            'category' => 'Cottage'
        ]);
    
        // Process images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('imgs/facility_img'), $imageName);
                
                FacilityImage::create([
                    'fac_id' => $cottage->id,
                    'image' => $imageName,
                    'order' => FacilityImage::where('fac_id', $cottage->id)->count() + 1
                ]);
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Cottage created successfully',
            'cottage' => $cottage->load('images')
        ]);
    }
    
    // Get a single cottage with images
    public function showCottage($id)
    {
        try {
            $cottage = Facility::with(['images' => function($query) {
                $query->orderBy('order', 'asc');
            }])->findOrFail($id);
    
            // Transform images to include URLs
            $cottage->images->transform(function($image) {
                $image->url = asset('imgs/facility_img/' . $image->image);
                return $image;
            });
    
            return response()->json([
                'success' => true,
                'cottage' => $cottage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cottage not found'
            ], 404);
        }
    }
    
    // Update cottage details and images
    public function updateCottage(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
    
        $cottage = Facility::findOrFail($id);
        $cottage->update([
            'name' => $validatedData['name'],
            'quantity' => $validatedData['quantity'],
            'price' => $validatedData['price'],
            'description' => $validatedData['description'] ?? null
        ]);
    
        // Process new images
        if ($request->hasFile('images')) {
            $this->processImages($request->file('images'), $cottage->id);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Cottage updated successfully',
            'cottage' => $cottage->load('images')
        ]);
    }
    
    // Delete a cottage and its images
    public function destroyCottage($id)
    {
        $cottage = Facility::findOrFail($id);
        
        // Delete all associated images
        foreach ($cottage->images as $image) {
            $imagePath = public_path('imgs/facility_img/' . $image->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $image->delete();
        }
        
        $cottage->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Cottage deleted successfully'
        ]);
    }
    
    // Get a single cottage with images
    public function editCottage($id)
    {
        try {
            $cottage = Facility::with(['images' => function($query) {
                $query->orderBy('order', 'asc');
            }])->findOrFail($id);
    
            // Transform images to include URLs
            $transformedImages = $cottage->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('imgs/facility_img/' . $image->image),
                    'image' => $image->image
                ];
            });
    
            return response()->json([
                'success' => true,
                'cottage' => $cottage,
                'images' => $transformedImages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cottage not found'
            ], 404);
        }
    }
    
    // Delete cottage image
    public function deleteCottageImage($id)
    {
        // Validate that the ID is a valid numeric value
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:image_fac,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image ID.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Proceed with deletion
        $image = FacilityImage::findOrFail($id);
        $imagePath = public_path('imgs/facility_img/' . $image->image);
    
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }
    
        $image->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
        ]);
    }
    
}
