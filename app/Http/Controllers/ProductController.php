<?php

namespace App\Http\Controllers;

use App\Models\product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // public function __construct(){
    //     $this->middleware('AdminAuthGuard',['except'=>['index']]);
    // }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = product::with('category')->get();
        return response()->json($products, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $request->validate([
                'title' => 'required|max:255',
                'brand' => 'required|max:255',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable|image',
                'details' => 'required',
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product = Product::create([
                'title' => $request->title,
                'brand' => $request->brand,
                'image' => $imagePath ?? null,
                'details' => $request->details,
                'price' => $request->price,
                'category_id' => $request->category_id,
            ]);

            return response()->json($product, 201);
        }catch(Exception $e){
            return response()->json([
                'message' => 'Error occurred during create',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'title' => 'required|max:255',
                'brand' => 'required|max:255',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable|image',
                'details' => 'required',
                'category_id' => 'required|exists:categories,id',
            ]);

            $product = Product::findOrFail($id);

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
            }

            $product->title = $request->title;
            $product->brand = $request->brand;
            $product->price = $request->price;
            $product->details = $request->details;
            $product->category_id = $request->category_id;

            $product->save();

            return response()->json($product, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error occurred during update',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json(['message' => 'Product deleted successfully'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error, Can not find this product']);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Product::query();

            if ($request->has('name')) {
                $query->where('title', 'LIKE', '%' . $request->name . '%');
            }

            if ($request->has('brand')) {
                $query->where('brand', 'LIKE', '%' . $request->brand . '%');
            }

            if ($request->has('price')) {
                $query->where('price', '<=', $request->price);
            }

            $products = $query->get();

            return response()->json($products, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error Not Found',
            ], 500);
        }
    }

}
