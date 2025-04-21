<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Category_manual;
use App\Models\Category_auto;
use App\Models\Field;
use App\Models\Field_query_relation;
use App\Models\Product;
use App\Models\Product_type;
use App\Models\Query;
use Illuminate\Http\Request;
use Illuminate\Support\str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
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
        $cats =  Category::with('catrule')
        ->where('shop_id', $shopId)
        ->where('cat_title', 'like', '%' . $request->search . '%')
        ->paginate($perPage);

        return view('user.category.index',compact('cats', 'perPage'));
    }

    public function add()
    {
        $shopId = session('selected_shop_id');
        $fields = Field::all(); 
        $queries = Query::all();
        $field_query_relations = Field_query_relation::all(); 
        $products = Product::where('shop_id', $shopId)->get();
        $type = Product_type::where('shop_id', $shopId)->get();
        $brands = Brand::where('shop_id', $shopId)->get();

        return view('user.category.add', compact(
            'fields', 
            'queries', 
            'field_query_relations', 
            'products',
            'type',
            'brands'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255', 
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'categorytype' => 'required|string|in:manual,auto',
            'fields' => 'array|nullable',
            'fields.*' => 'required_with:queries,values|exists:fields,field_id',
            'queries' => 'array|nullable',
            'queries.*' => 'required_with:fields,values|exists:queries,query_id',
            'values' => 'array|nullable',
            'matchType' => 'required_if:categorytype,auto|string|in:any,all', 
            'selected_product' => 'nullable|array',
        ],[
            'title.required' => 'Title is required.',
            'title.string' => 'Enter a valid title.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.string' => 'Enter a valid description.',
            'image.image' => 'Uploaded file must be an image.',
            'image.max' => 'Image size must be less than 2 MB.',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, webp.',
            'categorytype.required' => 'Category type is required.',
        ]);
        $shopId = session('selected_shop_id');
    
        $path = null;
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');        
            $filename = 'category_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath())
                ->resize(600, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
            $path = 'goapp/images/category/' . $filename;
            Storage::disk('s3')->put($path, (string)$img->encode());
        }
    
        $category = Category::create([
            'cat_title' => $data['title'],
            'cat_slug' => Str::slug(strtolower($data['title']), '-') . '-' . uniqid(),
            'cat_desc' => $data['description'] ?? null,
            'cat_image' => $path,
            'cat_type' => $data['categorytype'],
            'shop_id' => $shopId
        ]);
    
        
        if ($data['categorytype'] === 'auto' && isset($data['fields'])) {
            foreach ($data['fields'] as $index => $field) {
            
                if (!isset($data['queries'][$index], $data['values'][$index])) {
                    continue;
                }    
                  
                Category_auto::create([
                    'cat_id' => $category->cat_id,
                    'field_id' => $field,
                    'query_id' => $data['queries'][$index] ?? null, 
                    'value' => $data['values'][$index] ?? null,
                    'logical_operator' => $data['matchType'], 
                ]);
            }
        }else{
            Category_manual::create([
                'cat_id' => $category->cat_id,
                'product_ids' => $data['selected_product'] ?? [],
            ]);        
        }
    
        return redirect()->route('category.index')->with('success', 'Category create successfully!');
    }



    public function view($cat_id)
    {
        $category = Category::findOrFail($cat_id);

        if ($category->cat_type === 'auto') {
            // Fetch category rules
            $categoryRules = Category_auto::with(['queryRelation', 'field'])
                ->where('cat_id', $cat_id)
                ->get();

            // Start the product query
            $productsQuery = Product::join('variants', 'products.product_id', '=', 'variants.product_id')
                ->join('variant_details', 'variant_details.variant_id', '=', 'variants.variant_id')
                ->join('product_types', 'product_types.product_type_id', '=', 'variants.product_type_id')
                ->join('brands', 'brands.brand_id', '=', 'variants.brand_id')
                ->select('products.*', 'variants.*', 'variant_details.*', 'product_types.*', 'brands.*');

            $logicalOperator = $categoryRules->first()->logical_operator ?? 'all';

            if ($categoryRules->isEmpty()) {
                $products = collect(); // Return an empty collection
            } else {
                if ($logicalOperator === 'all') {
                    foreach ($categoryRules as $rule) {
                        if (!isset($rule->field->product_field_name)) {
                            continue;
                        }
                    
                        switch ($rule->queryRelation->query_name) {
                            case 'is equal to':
                                $productsQuery->where($rule->field->product_field_name, '=', $rule->value);
                                break;
                            case 'is not equal to':
                                $productsQuery->where($rule->field->product_field_name, '!=', $rule->value);
                                break;
                            case 'starts with':
                                $productsQuery->where($rule->field->product_field_name, 'LIKE',  $rule->value . '%');
                                break;
                            case 'ends with':
                                $productsQuery->where($rule->field->product_field_name, 'LIKE', '%' . $rule->value);
                                break;
                            case 'contains':
                                $productsQuery->where($rule->field->product_field_name, 'LIKE', '%' . $rule->value . '%');
                                break;
                            case 'does not contains':
                                $productsQuery->where($rule->field->product_field_name, 'NOT LIKE', '%' . $rule->value . '%');
                                break;
                            case 'greater than':
                                $productsQuery->where($rule->field->product_field_name, '>', $rule->value);
                                break;
                            case 'less than':
                                $productsQuery->where($rule->field->product_field_name, '<', $rule->value);
                                break;
                        }
                    }
                } elseif ($logicalOperator === 'any') {
                    $productsQuery->where(function ($query) use ($categoryRules) {
                        foreach ($categoryRules as $rule) {
                            if (!isset($rule->field->product_field_name)) {
                                continue;
                            }
                        
                            $query->orWhere(function ($subQuery) use ($rule) {
                                switch ($rule->queryRelation->query_name) {
                                    case 'is equal to':
                                        $subQuery->where($rule->field->product_field_name, '=', $rule->value);
                                        break;
                                    case 'is not equal to':
                                        $subQuery->where($rule->field->product_field_name, '!=', $rule->value);
                                        break;
                                    case 'starts with':
                                        $subQuery->where($rule->field->product_field_name, 'LIKE',  $rule->value . '%');
                                        break;
                                    case 'ends with':
                                        $subQuery->where($rule->field->product_field_name, 'LIKE', '%' . $rule->value);
                                        break;
                                    case 'contains':
                                        $subQuery->where($rule->field->product_field_name, 'LIKE', '%' . $rule->value . '%');
                                        break;
                                    case 'does not contains':
                                        $subQuery->where($rule->field->product_field_name, 'NOT LIKE', '%' . $rule->value . '%');
                                        break;
                                    case 'greater than':
                                        $subQuery->where($rule->field->product_field_name, '>', $rule->value);
                                        break;
                                    case 'less than':
                                        $subQuery->where($rule->field->product_field_name, '<', $rule->value);
                                        break;
                                }
                            });
                        }
                    });
                }

                $shopId = session('selected_shop_id');
                $products = $productsQuery->where('products.shop_id', $shopId)->get();
            }
        } else {
            // Fetch manually selected products
            $categoryManual = Category_manual::where('cat_id', $cat_id)->first();
            $productIds = $categoryManual ? $categoryManual->product_ids : [];
            $products = Product::whereIn('product_id', $productIds)->with('variants')->get();
        }

        return view('user.category.view', ['products' => $products]);
    }


   
    public function edit($cat_id)
    {
        // Fetch the category to edit
        $category = Category::findOrFail($cat_id);

        if ($category->cat_type === 'auto') {
            // Fetch category rules
            $categoryRules = Category_auto::with(['queryRelation', 'field'])
                ->where('cat_id', $cat_id)
                ->get();

            // Start the product query with correct table references
            $productsQuery = Product::join('variants', 'products.product_id', '=', 'variants.product_id')
                ->join('variant_details', 'variant_details.variant_id', '=', 'variants.variant_id')
                ->join('product_types', 'product_types.product_type_id', '=', 'variants.product_type_id')
                ->join('brands', 'brands.brand_id', '=', 'variants.brand_id')
                ->select('products.*', 'variants.*', 'variant_details.*', 'product_types.*', 'brands.*');

            // Logical operator: 'all' or 'any'
            $logicalOperator = $categoryRules->first()->logical_operator ?? 'all';
            if ($categoryRules->isEmpty()) {
                $product_auto = collect();
            } else {
                if ($logicalOperator === 'all') {
                    foreach ($categoryRules as $rule) {
                        if (!isset($rule->field->product_field_name)) {
                            continue;
                        }

                        switch ($rule->queryRelation->query_name) {
                            case 'is equal to':
                                $productsQuery->where($rule->field->product_field_name, '=', $rule->value);
                                break;
                            case 'is not equal to':
                                $productsQuery->where($rule->field->product_field_name, '!=', $rule->value);
                                break;
                            case 'starts with':
                                $productsQuery->where($rule->field->product_field_name, 'LIKE', $rule->value . '%');
                                break;
                            case 'ends with':
                                $productsQuery->where($rule->field->product_field_name, 'LIKE', '%' . $rule->value);
                                break;
                            case 'contains':
                                $productsQuery->where($rule->field->product_field_name, 'LIKE', '%' . $rule->value . '%');
                                break;
                            case 'does not contains':
                                $productsQuery->where($rule->field->product_field_name, 'NOT LIKE', '%' . $rule->value . '%');
                                break;
                            case 'greater than':
                                $productsQuery->where($rule->field->product_field_name, '>', $rule->value);
                                break;
                            case 'less than':
                                $productsQuery->where($rule->field->product_field_name, '<', $rule->value);
                                break;
                        }
                    }
                } elseif ($logicalOperator === 'any') {
                    $productsQuery->where(function ($query) use ($categoryRules) {
                        foreach ($categoryRules as $rule) {
                            if (!isset($rule->field->product_field_name)) {
                                continue;
                            }

                            $query->orWhere(function ($subQuery) use ($rule) {
                                switch ($rule->queryRelation->query_name) {
                                    case 'is equal to':
                                        $subQuery->where($rule->field->product_field_name, '=', $rule->value);
                                        break;
                                    case 'is not equal to':
                                        $subQuery->where($rule->field->product_field_name, '!=', $rule->value);
                                        break;
                                    case 'starts with':
                                        $subQuery->where($rule->field->product_field_name, 'LIKE', $rule->value . '%');
                                        break;
                                    case 'ends with':
                                        $subQuery->where($rule->field->product_field_name, 'LIKE', '%' . $rule->value);
                                        break;
                                    case 'contains':
                                        $subQuery->where($rule->field->product_field_name, 'LIKE', '%' . $rule->value . '%');
                                        break;
                                    case 'does not contains':
                                        $subQuery->where($rule->field->product_field_name, 'NOT LIKE', '%' . $rule->value . '%');
                                        break;
                                    case 'greater than':
                                        $subQuery->where($rule->field->product_field_name, '>', $rule->value);
                                        break;
                                    case 'less than':
                                        $subQuery->where($rule->field->product_field_name, '<', $rule->value);
                                        break;
                                }
                            });
                        }
                    });
                }
                $shopId = session('selected_shop_id');
                $product_auto = $productsQuery->where('products.shop_id', $shopId)->get();
            }
        } else {
            // Fetch manually selected products
            $categoryManual = Category_manual::where('cat_id', $cat_id)->first();
            $productIds = $categoryManual ? $categoryManual->product_ids : [];
            $product_auto = Product::whereIn('product_id', $productIds)->with('variants')->get();
        }

        // Fetch fields, queries, and relations for form dropdowns
        $fields = Field::all();
        $queries = Query::all();
        $field_query_relations = Field_query_relation::all();

        $shopId = session('selected_shop_id');

        $type = Product_type::where('shop_id', $shopId)->get();
        $brands = Brand::where('shop_id', $shopId)->get();
        $products = Product::where('shop_id', $shopId)->get();

        // Initialize variables
        $manualCheckedProducts = [];
        $categoryRules = [];
        $logicalOperator = 'all';

        if ($category->cat_type === 'manual') {
            // Fetch selected products for manual type
            $categoryManual = Category_manual::where('cat_id', $cat_id)->first();
            $manualCheckedProducts = $categoryManual ? $categoryManual->product_ids : [];
        } elseif ($category->cat_type === 'auto') {
            // Fetch rules for auto type
            $categoryRules = Category_auto::with(['queryRelation', 'field'])
                ->where('cat_id', $cat_id)
                ->get();

            $logicalOperator = $categoryRules->first()->logical_operator ?? 'all';
        }

        return view('user.category.edit', compact(
            'category',
            'fields',
            'queries',
            'field_query_relations',
            'products',
            'manualCheckedProducts',
            'categoryRules',
            'logicalOperator',
            'product_auto',
            'type',
            'brands'
        ));
    }



    public function update(Request $request, $cat_id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'selected_product' => 'nullable|array',
            'fields' => 'nullable|array',
            'queries' => 'nullable|array',
            'values' => 'nullable|array',
            'matchType' => 'required_if:categorytype,auto|string|in:any,all',
        ],[
            'title.required' => 'Title is required.',
            'title.string' => 'Enter a valid title.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.string' => 'Enter a valid description.',
            'image.image' => 'Uploaded file must be an image.',
            'image.max' => 'Image size must be less than 2 MB.',
            'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif, webp.',
        ]);

        // Find the category
        $category = Category::findOrFail($cat_id);

        $path = $category->cat_image;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'category_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath())
                ->resize(600, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
            $path = 'goapp/images/category/' . $filename;
            if ($category->cat_image && Storage::disk('s3')->exists($category->cat_image)) {
                Storage::disk('s3')->delete($category->cat_image);
            }
            Storage::disk('s3')->put($path, (string)$img->encode());
        }

        // Update category details
        $category->update([
            'cat_title' => $data['title'],
            'cat_slug' => Str::slug(strtolower($data['title']), '-') . '-' . uniqid(),
            'cat_image' => $path,
            'cat_desc' => $data['description'] ?? null,
        ]);

        // Handle Manual Category Update
        if ($category->cat_type === 'manual') {
            Category_manual::updateOrCreate(
                ['cat_id' => $category->cat_id],
                ['product_ids' => $data['selected_product'] ?? []] // Ensure empty array if no selection
            );
        }

        // Handle Auto Category Update
        if ($category->cat_type === 'auto' && isset($data['fields']) && count($data['fields']) > 0) {
            // Remove existing conditions
            Category_auto::where('cat_id', $category->cat_id)->delete();
        
            foreach ($data['fields'] as $index => $fieldId) {
                // Skip if query/value is missing
                if (!isset($data['queries'][$index]) || !isset($data['values'][$index])) {
                    continue;
                }

                Category_auto::create([
                    'cat_id' => $category->cat_id,
                    'field_id' => $fieldId,
                    'query_id' => $data['queries'][$index],
                    'value' => $data['values'][$index],
                    'logical_operator' => $data['matchType'],
                ]);
            }
        }
        return back()->with('success', 'Category updated successfully!');
    }
}
