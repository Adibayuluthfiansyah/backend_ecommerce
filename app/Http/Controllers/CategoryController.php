<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255|unique:categories,name', // Ubah dari 'name' ke 'nama'
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'boolean'
            ]);

            $category = new Category();
            $category->name = $request->nama; // Ubah dari 'name' ke 'nama'
            $category->slug = Str::slug($request->nama); // Ubah dari 'name' ke 'nama'
            $category->description = $request->description;
            $category->is_active = $request->has('is_active') ? (bool)$request->is_active : true;

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
                $category->image = $imagePath;
            }

            $category->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255|unique:categories,name,' . $category->id, // Ubah dari 'name' ke 'nama'
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'boolean'
            ]);

            $category->name = $request->nama; // Ubah dari 'name' ke 'nama'
            $category->slug = Str::slug($request->nama); // Ubah dari 'name' ke 'nama'
            $category->description = $request->description;
            $category->is_active = $request->has('is_active') ? (bool)$request->is_active : $category->is_active;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }

                $imagePath = $request->file('image')->store('categories', 'public');
                $category->image = $imagePath;
            }

            $category->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            // Delete image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active categories only
     */
    public function getActiveCategories(): JsonResponse
    {
        $categories = Category::active()->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}
