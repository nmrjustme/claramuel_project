<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\FacilityImage;
use App\Models\FacilityDiscount;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DayTourFacilitiesController extends Controller
{
    public function daytourIndex()
    {
        $facilities = Facility::with(['images', 'discounts'])
            ->where('type', 'day-tour') // CORRECT: Use 'type' for day-tour/overnight
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.facilities.cottage', ['facilities' => $facilities]);
    }

    /**
     * Display a listing of day-tour facilities (API)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        // CORRECT: Use 'type' column for day-tour filtering
        $facilities = Facility::with('images')
            ->where('type', 'day-tour') // Filter by type = day-tour
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        // Transform the data to include full image URLs
        $transformed = $facilities->getCollection()->map(function($facility) {
            $facility->images->transform(function($image) {
                $image->url = asset('imgs/facility_img/' . $image->image);
                return $image;
            });
            return $facility;
        });
        
        $facilities->setCollection($transformed);
        
        return response()->json([
            'success' => true,
            'data' => $facilities->items(),
            'current_page' => $facilities->currentPage(),
            'last_page' => $facilities->lastPage(),
            'total' => $facilities->total(),
            'from' => $facilities->firstItem(),
            'to' => $facilities->lastItem(),
            'per_page' => $facilities->perPage(),
        ]);
    }

    /**
     * Store a newly created day-tour facility
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'category' => 'required|string|max:255', // CORRECT: This is category (Cottage, Villa, etc.)
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'rate_type' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'description' => 'nullable|string',
            'included' => 'nullable|string|max:100',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Create the facility - type is always 'day-tour' for this controller
        $facility = Facility::create([
            'name' => $validatedData['name'],
            'type' => 'day-tour', // CORRECT: Always set to 'day-tour'
            'category' => $validatedData['category'], // This is the specific category (Cottage, Villa, etc.)
            'quantity' => $validatedData['quantity'],
            'price' => $validatedData['price'],
            'rate_type' => $validatedData['rate_type'],
            'status' => $validatedData['status'],
            'description' => $validatedData['description'] ?? null,
            'included' => $validatedData['included'] ?? null,
        ]);

        // Process images
        if ($request->hasFile('images')) {
            $this->processImages($request->file('images'), $facility->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Day-tour facility created successfully',
            'facility' => $facility->load('images')
        ]);
    }

    /**
     * Display the specified day-tour facility
     */
    public function show($id)
    {
        try {
            // CORRECT: Use 'type' for day-tour filtering
            $facility = Facility::with(['images' => function($query) {
                $query->orderBy('order', 'asc');
            }])->where('type', 'day-tour')->findOrFail($id);

            // Transform images to include URLs
            $facility->images->transform(function($image) {
                $image->url = asset('imgs/facility_img/' . $image->image);
                return $image;
            });

            return response()->json([
                'success' => true,
                'facility' => $facility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Day-tour facility not found'
            ], 404);
        }
    }

    /**
     * Edit the specified day-tour facility
     */
    public function edit($id)
    {
        try {
            // CORRECT: Use 'type' for day-tour filtering
            $facility = Facility::with(['images' => function($query) {
                $query->orderBy('order', 'asc');
            }])->where('type', 'day-tour')->findOrFail($id);

            // Transform images to include URLs
            $transformedImages = $facility->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('imgs/facility_img/' . $image->image),
                    'image' => $image->image
                ];
            });

            return response()->json([
                'success' => true,
                'facility' => $facility,
                'images' => $transformedImages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Day-tour facility not found'
            ], 404);
        }
    }

    /**
     * Update the specified day-tour facility
     */
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:50',
                'category' => 'required|string|max:255', // CORRECT: This is category (Cottage, Villa, etc.)
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'rate_type' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'description' => 'nullable|string',
                'included' => 'nullable|string|max:100',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new HttpResponseException(
                response()->json(['errors' => $e->errors()], 422)
            );
        }

        // CORRECT: Use 'type' for day-tour filtering
        $facility = Facility::where('type', 'day-tour')->findOrFail($id);

        $facility->update($validatedData);

        // Process new images
        if ($request->hasFile('images')) {
            $this->processImages($request->file('images'), $facility->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Day-tour facility updated successfully',
            'facility' => $facility->load('images'),
        ]);
    }

    /**
     * Remove the specified day-tour facility
     */
    public function destroy($id)
    {
        // CORRECT: Use 'type' for day-tour filtering
        $facility = Facility::where('type', 'day-tour')->findOrFail($id);
        
        // Delete all associated images
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
            'message' => 'Day-tour facility deleted successfully'
        ]);
    }

    /**
     * Get images for a specific day-tour facility
     */
    public function getImages($id)
    {
        try {
            // CORRECT: Use 'type' for day-tour filtering
            $facility = Facility::with('images')->where('type', 'day-tour')->find($id);
            
            if (!$facility) {
                return response()->json([
                    'success' => false,
                    'message' => 'Day-tour facility not found'
                ], 404);
            }

            // Transform images collection
            $transformedImages = $facility->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('imgs/facility_img/' . $image->image),
                    'image' => $image->image
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
            \Log::error('Failed to fetch day-tour facility images: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred'
            ], 500);
        }
    }

    /**
     * Delete a specific image
     */
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
        
        // CORRECT: Use 'type' for day-tour filtering
        $facility = Facility::where('id', $image->fac_id)
            ->where('type', 'day-tour')
            ->first();
            
        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found or does not belong to a day-tour facility.'
            ], 404);
        }

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

    /**
     * Process and store images
     */
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

    /**
     * Update image order (for drag and drop sorting)
     */
    public function updateImageOrder(Request $request)
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|integer|exists:image_fac,id',
            'images.*.order' => 'required|integer|min:1'
        ]);

        try {
            foreach ($validated['images'] as $imageData) {
                FacilityImage::where('id', $imageData['id'])->update([
                    'order' => $imageData['order']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Image order updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update image order'
            ], 500);
        }
    }

    // =============================================
    // DISCOUNT MANAGEMENT FOR DAY-TOUR FACILITIES
    // =============================================

     /**
     * Get discounts for a specific day-tour facility
     */
    public function getDiscounts($facilityId)
    {
        // CORRECT: Use 'type' for day-tour filtering
        $facility = Facility::where('id', $facilityId)
            ->where('type', 'day-tour')
            ->first();

        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Day-tour facility not found'
            ], 404);
        }

        $discounts = FacilityDiscount::where('facility_id', $facilityId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'discounts' => $discounts
        ]);
    }

    /**
     * Add discount to a day-tour facility
     */
    public function addDiscount(Request $request, $facilityId)
    {
        // Verify the facility is a day-tour facility
        $facility = Facility::where('id', $facilityId)
            ->where('type', 'day-tour')
            ->first();

        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Day-tour facility not found'
            ], 404);
        }

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

    /**
     * Update discount for a day-tour facility
     */
    public function updateDiscount(Request $request, $discountId)
    {
        $discount = FacilityDiscount::findOrFail($discountId);
        
        // Verify the discount belongs to a day-tour facility
        $facility = Facility::where('id', $discount->facility_id)
            ->where('type', 'day-tour')
            ->first();

        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Discount not found or does not belong to a day-tour facility'
            ], 404);
        }

        $isCurrentlyActive = now()->between($discount->start_date, $discount->end_date);

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
                function ($attribute, $value, $fail) use ($facility, $request, $discount) {
                    $this->validateDiscountDates($facility->id, $value, $request->end_date, $discount->id, $fail);
                }
            ],
            'end_date' => 'required|date|after:start_date',
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

    /**
     * Delete discount from a day-tour facility
     */
    public function deleteDiscount($discountId)
    {
        $discount = FacilityDiscount::findOrFail($discountId);
        
        // Verify the discount belongs to a day-tour facility
        $facility = Facility::where('id', $discount->facility_id)
            ->where('type', 'day-tour')
            ->first();

        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Discount not found or does not belong to a day-tour facility'
            ], 404);
        }

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
}