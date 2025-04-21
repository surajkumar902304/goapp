<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Option_name;
use App\Models\Product;
use App\Models\Product_tag;
use App\Models\Product_type;
use App\Models\Tag;
use App\Models\Variant;
use App\Models\Variant_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\str;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','check.user.shop']);
    }

    public function index(Request $request)
    {
        $shopId = session('selected_shop_id');
        $search = $request->get('search', '');
        $perPage = $request->get('perPage', 10);

        $products = Product::where('shop_id', $shopId)
        ->where('product_title', 'like', '%' . $request->search . '%')
        ->with('atags','variants_index')
        ->paginate($perPage);
        return view('user.products.index', compact('products', 'perPage'));
    }

    public function add() 
    {
        $userId = Auth::user()->id;
        $shopId = session('selected_shop_id');
        $ptypes = Product_type::where('shop_id', $shopId)->where('product_type_status', 1)->get();
        $brands = Brand::where('shop_id', $shopId)->where('brand_status', 1)->get();
        $tags = Tag::where('shop_id', $shopId)->where('tag_status', 1)->get();
        $options = Option_name::get();
        return view('user.products.add', compact('ptypes', 'brands', 'tags', 'options'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'sku' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request) {
                    $shopId = session('selected_shop_id');
                    $existingsku = Variant::where('sku', $value)
                        ->join('products', 'products.product_id', '=', 'variants.product_id')
                        ->where('products.shop_id', $shopId)
                        ->first();
                    
                    if ($existingsku) {
                        $fail('SKU already exists within the selected shop.');
                    }
                },
            ],
            'qty' => 'nullable|integer',
            'price' => 'required|numeric|min:1',
            'product_type' => 'required',
            'brand' => 'required',
            'tags' => 'nullable|array',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ],[
            'title.required' => 'Title is required.',
            'title.string' => 'Enter a valid title.',
            'title.min' => 'Title must be at least 3 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'sku.required' => 'SKU is required.',
            'sku.string' => 'Enter a valid SKU.',
            'sku.max' => 'SKU cannot exceed 50 characters.',
            'qty.integer' => 'Quantity must be an integer.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a numeric value.',
            'price.min' => 'Price must be at least 1.',
            'product_type.required' => 'Please select a product Type',
            'brand.required' => 'Please select a brand',
            'image.required' => 'Product image is required.',
            'image.image' => 'Uploaded file must be an image.',
            'image.max' => 'Image size must be less than 2 MB.',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, webp.',
        ]);
        
        $shopId = session('selected_shop_id');

        $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'product_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath())
                ->resize(600, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
            $path = 'goapp/images/product/' . $filename;
            Storage::disk('s3')->put($path, (string)$img->encode());
        }

        $product = Product::create([
            'product_title' => $validatedData['title'],
            'product_slug' => Str::slug(strtolower($validatedData['title']),'-')."-".uniqid(),
            'product_image' => $path,
            'shop_id' => $shopId,
        ]);
        $qty = $validatedData['qty'] ?? 10;
        $variant = Variant::create([
            'sku' => $validatedData['sku'],
            'qty' => $qty,
            'price' => $validatedData['price'],
            'product_id' => $product->product_id,
            'product_type_id' => $validatedData['product_type'],
            'brand_id' => $validatedData['brand'],
        ]);
        if (!empty($validatedData['tags'])) {
            foreach ($validatedData['tags'] as $tagId) {
                Product_tag::create([
                    'product_id' => $product->product_id,
                    'tag_id' => $tagId,
                ]);
            }
        }
        if (!empty($validatedData['options'])) {
            Variant_detail::create([
                'variant_id' => $variant->variant_id,
                'option_ids' => array_keys($validatedData['options']),
                'options' => $validatedData['options'],
            ]);
        }else{
            Variant_detail::create([
                'variant_id' => $variant->variant_id,
                'option_ids' => [],
                'options' => [],
            ]);
        }
        return redirect()->route('product.index')->with('success', 'Product added successfully!');
    }

    public function edit($product_id)
    {
        $shopId = session('selected_shop_id');
        $product = Product::with(['variants', 'tags'])->findOrFail($product_id);
        $productTypes = Product_Type::where('shop_id', $shopId)->where('product_type_status','=','1')->get();
        $brands = Brand::where('shop_id', $shopId)->where('brand_status','=','1')->get();
        $tags = Tag::where('shop_id', $shopId)->where('tag_status','=','1')->get();
        $options = Option_name::all();
        $selectedOptions = $product->variants->details->pluck('options', 'value')->toArray();
        
        return view('user.products.edit', compact('product', 'productTypes', 'brands', 'tags', 'options', 'selectedOptions'));
    }
    
    public function update(Request $request, $product_id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'sku' => [
            'required',
            'string',
            'max:50',
            function ($attribute, $value, $fail) use ($request) {
                    $shopId = session('selected_shop_id');
                    $productId = $request->route('product_id');
                
                    $existingSKU = Variant::where('sku', $value)
                        ->join('products', 'products.product_id', '=', 'variants.product_id')
                        ->where('products.shop_id', $shopId)
                        ->first();
                
                    if ($existingSKU && $existingSKU->product_id != $productId) {
                        $fail('SKU already exists within the selected shop.');
                    }
                },
            ],
            'qty' => 'required|integer',
            'price' => 'required|numeric|min:1',
            'product_type' => 'required',
            'brand' => 'required',
            'tags' => 'nullable|array',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ],[
            'title.required' => 'Title is required.',
            'title.string' => 'Enter a valid title.',
            'title.min' => 'Title must be at least 3 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'sku.required' => 'SKU is required.',
            'sku.string' => 'Enter a valid SKU.',
            'sku.max' => 'SKU cannot exceed 50 characters.',
            'qty.integer' => 'Quantity must be an integer.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a numeric value.',
            'price.min' => 'Price must be at least 1.',
            'product_type.required' => 'Please select a product Type',
            'brand.required' => 'Please select a brand',
            'image.image' => 'Uploaded file must be an image.',
            'image.max' => 'Image size must be less than 2 MB.',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, webp.',
        ]);
        
        $product = Product::findOrFail($product_id);
        $variant = $product->variants()->first();

        if (!$variant) {
            return back()->withErrors(['error' => 'No variant found for the product.']);
        }

        $path = $product->product_image;

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $filename = 'product_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath())
                ->resize(600, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
            $path = 'goapp/images/product/' . $filename;

            if ($product->product_image && Storage::disk('s3')->exists($product->product_image)) {
                Storage::disk('s3')->delete($product->product_image);
            }

            Storage::disk('s3')->put($path, (string)$img->encode());
        }

        $product->update([
            'product_title' => $validatedData['title'],
            'product_slug' => Str::slug(strtolower($validatedData['title']),'-')."-".uniqid(),
            'product_image' => $path,
        ]);
        $variant->update([
            'sku' => $validatedData['sku'],
            'qty' => $validatedData['qty'],
            'price' => $validatedData['price'],
            'product_type_id' => $validatedData['product_type'],
            'brand_id' => $validatedData['brand'],
        ]);
        Product_tag::where('product_id', $product->product_id)->delete();
        if (!empty($validatedData['tags'])) {
            foreach ($validatedData['tags'] as $tagId) {
                Product_tag::create([
                    'product_id' => $product->product_id,
                    'tag_id' => $tagId,
                ]);
            }
        }
        if (!empty($validatedData['options'])) {
            $variantDetail = $variant->details()->first();
    
            if ($variantDetail) {
                $variantDetail->update([
                    'option_ids' => array_keys($validatedData['options']),
                    'options' => $validatedData['options'],
                ]);
            } else {
                Variant_detail::create([
                    'variant_id' => $variant->variant_id,
                    'option_ids' => array_keys($validatedData['options']),
                    'options' => $validatedData['options'],
                ]);
            }
        }
        return redirect()->route('product.index')->with('success', 'Product updated successfully!');
    }


    public function duplicate(Request $request, $product_id)
    {
        $originalProduct = Product::with(['variants'])->findOrFail($product_id);

        $shopId = session('selected_shop_id');
        
        $baseSku = $originalProduct->variants->sku;
        $existingSku = Variant::where('sku', 'LIKE', "$baseSku-copy%")
            ->orderByRaw("LENGTH(sku) DESC")
            ->orderBy('sku', 'DESC')
            ->pluck('sku')
            ->first();
    
        if ($existingSku) {
            preg_match('/\d+$/', $existingSku, $matches);
            $increment = isset($matches[0]) ? (int)$matches[0] + 1 : 1;
            $newSku = "{$baseSku}-copy-{$increment}";
        } else {
            $newSku = "{$baseSku}-copy-1";
        }
        
        $newProduct = Product::create([
            'product_title' => $originalProduct->product_title,
            'product_image' => $originalProduct->product_image,
            'shop_id' => $shopId,
            'product_slug' => Str::slug(strtolower($originalProduct->product_title), '-') . '-' . uniqid(),
        ]);
    
        if ($originalProduct->variants) {
            $newVariant = Variant::create([
                'sku' => $newSku,
                'qty' => $originalProduct->variants->qty,
                'price' => $originalProduct->variants->price,
                'product_id' => $newProduct->product_id,
                'product_type_id' => $originalProduct->variants->product_type_id,
                'brand_id' => $originalProduct->variants->brand_id,
            ]);
        
            if (!empty($originalProduct->variants->option_ids)) {
                Variant_detail::create([
                    'variant_id' => $newVariant->variant_id,
                    'option_ids' => json_decode($originalProduct->variants->option_ids, true),
                    'options' => json_decode($originalProduct->variants->options, true),
                ]);
            }                   
        }
    
        foreach ($originalProduct->atags as $originalTag) {
            Product_tag::create([
                'product_id' => $newProduct->product_id,
                'tag_id' => $originalTag->tag_id,
            ]);
        }
    
        return redirect()->route('product.edit', ['product_id' => $newProduct->product_id])
            ->with('success', 'Product duplicated successfully!');
    }

}
