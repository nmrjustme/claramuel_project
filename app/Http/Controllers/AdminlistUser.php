<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminlistUser extends Controller
{
    public function index()
    {
        return view('admin.user.index');
    }

    public function data()
    {
        // Get all users with admin role
        $admins = User::where('role', 'Admin')->get();

        // Get stats for the dashboard
        $stats = [
            'total' => User::where('role', 'Admin')->count(),
        ];

        return response()->json([
            'admins' => $admins,
            'stats' => $stats
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'temp_password' => 'required|string|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d).{8,}$/',
        ], [
            'temp_password.regex' => 'The password must be at least 8 characters and contain both letters and numbers.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->temp_password),
                'role' => 'Admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully',
                'admin' => $admin
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin = User::where('role', 'Admin')->findOrFail($id);

            $admin->firstname = $request->firstname;
            $admin->lastname = $request->lastname;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully',
                'admin' => $admin
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $admin = User::where('role', 'Admin')->findOrFail($id);

            // Prevent deletion of the currently logged-in admin
            if ($admin->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 422);
            }

            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword($id)
    {
        try {
            $admin = User::where('role', 'Admin')->findOrFail($id);

            $tempPassword = Str::random(10);
            $admin->password = Hash::make($tempPassword);
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'temp_password' => $tempPassword
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }
}