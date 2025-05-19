<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Field_query_relation;
use App\Models\MainCategory;
use App\Models\Mbrand;
use App\Models\Mcategory;
use App\Models\Mcollection_auto;
use App\Models\Mcollection_manual;
use App\Models\Mproduct;
use App\Models\Mproduct_type;
use App\Models\Msubcategory;
use App\Models\Mtag;
use App\Models\Query;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\str;

class McategoryController extends Controller
{
    // Main Category code below
    public function mainmcat()
    {
        return view('admin.mcategory.maincatlist');
    }
    
    public function mainMcatVlist()
    {
        $mainmcat = MainCategory::orderBy('main_mcat_id', 'desc')->get();
        return response()->json([
            'status' => true,
            'mainmcats' => $mainmcat,
        ],200);
    }

    public function addMainMcat(Request $request)
    {
        $request->validate([
            'main_mcat_name'  => 'required|string|max:255',
        ]);

        $cat = new MainCategory();
        $cat->main_mcat_name = $request->main_mcat_name;
        $cat->save();

        return response()->json(['status' => true]);
    }

    
    public function editMainMcat(Request $request)
    {
        $request->validate([
            'main_mcat_id'    => 'required|exists:main_categories,main_mcat_id',
            'main_mcat_name'  => 'required|string|max:50',
        ]);

        $cat = MainCategory::find($request->main_mcat_id);
        $cat->main_mcat_name = $request->main_mcat_name;
        $cat->save();

        return response()->json(['status' => true]);
    }

    public function deleteMainMcat(Request $request)
    {
        $request->validate([
            'main_mcat_id' => 'required|exists:main_categories,main_mcat_id',
        ]);

        try {
            $category = MainCategory::findOrFail($request->main_mcat_id);


            $category->delete();

            return response()->json(['status' => true, 'message' => 'Category deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // Category code below
    public function index()
    {
        return view('admin.mcategory.mcatlist');
    }
    
    public function mcatVlist()
    {
        $categories = Mcategory::with('mainCategory:main_mcat_id,main_mcat_name')->orderBy('mcat_id', 'desc')->get();

        $mcats = $categories->map(function ($cat) {
            return [
                'mcat_id'        => $cat->mcat_id,
                'mcat_name'      => $cat->mcat_name,
                'main_mcat_id'   => $cat->main_mcat_id,
                'mainmcat_name'  => optional($cat->mainCategory)->main_mcat_name,
            ];
        });
        return response()->json([
            'status' => true,
            'mcats' => $mcats,
        ],200);
    }

    public function addMcat(Request $request)
    {
        $request->validate([
            'mcat_name'  => 'required|string|max:50',
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
        ]);

        $cat = new Mcategory();
        $cat->mcat_name = $request->mcat_name;
        $cat->main_mcat_id = $request->main_mcat_id;
        $cat->save();

        return response()->json(['status' => true]);
    }

    
    public function editMcat(Request $request)
    {
        $request->validate([
            'mcat_id'    => 'required|exists:mcategories,mcat_id',
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_name'  => 'required|string|max:50',
        ]);

        $cat = Mcategory::find($request->mcat_id);
        $cat->mcat_name = $request->mcat_name;
        $cat->main_mcat_id = $request->main_mcat_id;
        $cat->save();

        return response()->json(['status' => true]);
    }

    public function deleteMcat(Request $request)
    {
        $request->validate([
            'mcat_id' => 'required|exists:mcategories,mcat_id',
        ]);

        try {
            $category = Mcategory::findOrFail($request->mcat_id);


            $category->delete();

            return response()->json(['status' => true, 'message' => 'Category deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // Sub-Category code below
    public function mcatsubList()
    {
        return view('admin.mcategory.msubcatlist');
    }
    
    public function mcatsubVlist()
    {
        $subs = Msubcategory::with(['category:mcat_id,mcat_name'])
        ->orderBy('msubcat_id', 'desc')
        ->get();
        return response()->json([
            'status' => true,
            'msubcats' => $subs,
        ],200);
    }

    public function productsVlist()
    {
        $products = Mproduct::where('status','=','Active')
                    ->WhereJsonContains('saleschannel', 'Online Store')
                    ->select('mproduct_id','mproduct_title','mproduct_image')->get();
        $types = Mproduct_type::get();
        $brands = Mbrand::get();
        $tags = Mtag::get();

        return response()->json([
            'status' => true,
            'products'=>$products,
            'types'=>$types,
            'brands'=>$brands,
            'tags'=>$tags,
        ],200);
    }

    public function querysVlist()
    {
        $querys = Query::select('query_id','query_name')->get();
        $fields = Field::select('field_id','field_name')->get();
        $relations = Field_query_relation::select('field_id','query_id')->get();
        return response()->json([
            'status' => true,
            'fields'=>$fields,
            'querys'=>$querys,
            'relations'=>$relations
        ],200);
    }

    public function addViewMsubcat()
    {
        return view('admin.mcategory.addsubcat');
    }

    public function addMsubcat(Request $request)
    {
        $validated = $request->validate([
            'mcat_id'    => ['required', 'integer', 'exists:mcategories,mcat_id'],
            'subcatname' => ['required', 'string', 'max:255',],
            'subcattag'  => ['nullable', 'string', 'max:255'],
            'msubcat_publish' => ['nullable'],
            'mcattype'   => ['required'],
            'image'      => ['required', 'image', 'max:2048'],

            'product_ids'      => ['nullable','string'],  
            'condition_logic'  => ['nullable'],
            'conditions'       => ['nullable','string'],  
            'offer_name'  => ['nullable', 'string', 'max:255', 'unique:msubcategories,offer_name'],
            'start_time'  => ['nullable', 'date', 'after_or_equal:now'],
            'end_time'    => ['nullable', 'date', 'after:start_time'],  
        ]);

    
        $subcatimagepath = null;
        if ($request->hasFile('image')) {
            $image  = $request->file('image');
            $filename = 'msubcat_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath())->resize(600, 800, function ($constraint) {
                $constraint->aspectRatio();
            });
            $subcatimagepath      = 'goapp/images/msub-categories/' . $filename;
            Storage::disk('s3')->put($subcatimagepath, (string) $img->encode());
        }

        $ids = [];

        if($validated['mcattype'] === 'manual')
        {
            $ids = json_decode($validated['product_ids'] ?? '[]', true);
        }

        $subcat = MSubCategory::create([
            'mcat_id'        => $validated['mcat_id'],
            'msubcat_name'   => $validated['subcatname'],
            'msubcat_slug'   => Str::slug(strtolower($validated['subcatname'])).'-'.uniqid(),
            'msubcat_tag'    => $validated['subcattag'] ?? null,
            'msubcat_publish'=> json_decode($validated['msubcat_publish'], true) ?? [],
            'msubcat_image'  => $subcatimagepath,
            'msubcat_type'   => $validated['mcattype'],
            'logical_operator'   => $validated['condition_logic'] ?? null,
            'product_ids'   => $ids,
            'offer_name'      => $validated['offer_name'] ?? null,
            'start_time'      => $validated['start_time'] ?? null,
            'end_time'        => $validated['end_time'] ?? null,
        ]);

        
        if ($validated['mcattype'] === 'smart') {

            $logic = $validated['condition_logic'] ?? 'all';
            $rows  = json_decode($validated['conditions'] ?? '[]', true);

            foreach ($rows as $row) {
                if (
                    empty($row['field_id'])   ||
                    empty($row['query_id'])   ||
                    $row['value'] === '' || $row['value'] === null
                ) {
                    continue;                           
                }

                Mcollection_auto::create([
                    'msubcat_id'      => $subcat->msubcat_id,
                    'field_id'        => $row['field_id'],
                    'query_id'        => $row['query_id'],
                    'value'           => $row['value'] ?? null,
                    'logical_operator'=> $logic
                ]);
            }
        } 
        
        return response()->json([
            'success' => true,
        ], 201);
    }
      
    public function msubcatEdit($msubcatid)
    {
        return view('admin.mcategory.editsubcat', ['msubcatid' => $msubcatid]);
    }

    public function msubcatEditData($msubcatid)
    {
        $subcat = Msubcategory::find($msubcatid);

        if (!$subcat) {
            return response()->json([
                'status'  => false,
                'message' => 'Sub‑Category not found'
            ], 404);
        }

        $conditions   = [];
        $logic        = 'all';

        if ($subcat->msubcat_type === 'smart') {

            $autoRows = Mcollection_auto::where('msubcat_id', $subcat->msubcat_id)->get();

            $conditions = $autoRows->map(fn ($r) => [
                'field_id' => $r->field_id,
                'query_id' => $r->query_id,
                'value'    => $r->value,
            ]);

        }

        return response()->json([
            'status' => true,
            'subcat' => [
                'mcat_id'         => $subcat->mcat_id,
                'msubcat_name'    => $subcat->msubcat_name,
                'msubcat_tag'     => $subcat->msubcat_tag,
                'msubcat_image'   => $subcat->msubcat_image,
                'msubcat_publish' => $subcat->msubcat_publish,
                'offer_name'      => $subcat->offer_name,
                'start_time'      => $subcat->start_time,
                'end_time'        => $subcat->end_time,
                'msubcat_type'    => $subcat->msubcat_type,
                'product_ids'     => $productIds = is_array($subcat->product_ids) ? $subcat->product_ids: (json_decode($subcat->product_ids, true) ?: []),
                'conditions'      => $conditions,
                'condition_logic' => $subcat->logical_operator,
            ],
        ]);
    }


    public function updateMsubcatData(Request $request)
    {
        $validated = $request->validate([
            'msubcat_id'  => 'required|exists:msubcategories,msubcat_id',
            'mcat_id'     => 'required|integer|exists:mcategories,mcat_id',
            'subcatname'  => 'required|string|max:255',
            'subcattag'   => 'nullable|string|max:255',
            'msubcat_publish'  => 'nullable',
            'mcattype'    => 'required|in:manual,smart',
            'image'       => 'nullable|image|max:2048',

            'product_ids'      => 'nullable|string',
            'condition_logic'  => 'nullable|in:all,any',
            'conditions'       => 'nullable|string',
            'offer_name'  => 'nullable', 'string', 'max:255', 'unique:msubcategories,offer_name',
            'start_time'  => 'nullable', 'date', 'after_or_equal:now',
            'end_time'    => 'nullable', 'date', 'after:start_time',
        ]);

        $sub = Msubcategory::find($validated['msubcat_id']);

        $pimagepath = $sub->msubcat_image;

        if ($request->hasFile('image')) {
            if (!empty($pimagepath) && Storage::disk('s3')->exists($pimagepath)) {
                Storage::disk('s3')->delete($pimagepath);
            }
            $image    = $request->file('image');
            $filename = 'msubcat_' . uniqid() . '.png';
            $img      = Image::make($image->getRealPath())
                            ->resize(600, 800, function($c){ $c->aspectRatio(); });
            $path     = 'goapp/images/msub-categories/' . $filename;
            Storage::disk('s3')->put($path, (string) $img->encode());
            $sub->msubcat_image = $path;
        }

        $sub->logical_operator = null;

        $sub->mcat_id         = $validated['mcat_id'];
        $sub->msubcat_name    = $validated['subcatname'];
        $sub->msubcat_tag     = $validated['subcattag'] ?? null;
        $sub->msubcat_publish = json_decode($validated['msubcat_publish'], true) ?? [];
        $sub->msubcat_type    = $validated['mcattype'];
        $sub->offer_name      = $validated['offer_name'] ?? null;
        $sub->start_time      = $validated['start_time'] ?? null;
        $sub->end_time        = $validated['end_time'] ?? null;
        

        if ($validated['mcattype'] === 'manual') {
            $sub->logical_operator = null;
            $sub->product_ids = json_decode($validated['product_ids'] ?? '[]', true);
            Mcollection_auto::where('msubcat_id', $sub->msubcat_id)->delete();
        } else {
            $sub->product_ids = [];
            $sub->logical_operator = $validated['condition_logic'];
            Mcollection_auto::where('msubcat_id', $sub->msubcat_id)->delete();

            $rows  = json_decode($validated['conditions'] ?? '[]', true);
            $logic = $validated['condition_logic'] ?? 'all';
        

            foreach ($rows as $row) {
                if (
                    empty($row['field_id'])   ||
                    empty($row['query_id'])   ||
                    $row['value'] === '' || $row['value'] === null
                ) {
                    continue;                           
                }

                Mcollection_auto::create([
                    'msubcat_id'      => $sub->msubcat_id,
                    'field_id'        => $row['field_id'],
                    'query_id'        => $row['query_id'],
                    'value'           => $row['value'],
                    'logical_operator'=> $logic,
                ]);
            }
        }

        $sub->save();

        return response()->json(
            [
                'status' => true, 
        ],201);
    }

    public function deleteMsubcat(Request $request)
    {
        $request->validate([
            'msubcat_id' => 'required|exists:msubcategories,msubcat_id',
        ]);

        try {
            $subcategory = Msubcategory::findOrFail($request->msubcat_id);

            if ($subcategory->msubcat_image && Storage::disk('s3')->exists($subcategory->msubcat_image)) {
                Storage::disk('s3')->delete($subcategory->msubcat_image);
            }

            $subcategory->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
