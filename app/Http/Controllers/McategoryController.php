<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Field_query_relation;
use App\Models\Mcategory;
use App\Models\Mcollection_auto;
use App\Models\Mcollection_manual;
use App\Models\Mproduct;
use App\Models\Msubcategory;
use App\Models\Query;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\str;

class McategoryController extends Controller
{
    // Category code below
    public function index()
    {
        return view('admin.mcategory.mcatlist');
    }
    
    public function mcatVlist()
    {
        $mcat = Mcategory::get();
        return response()->json([
            'status' => true,
            'mcats' => $mcat,
        ],200);
    }

    public function addMcat(Request $request)
    {
        $request->validate([
            'mcat_name'  => 'required|string|max:50',
        ]);

        $cat = new Mcategory();
        $cat->mcat_name = $request->mcat_name;
        $cat->save();

        return response()->json(['status' => true]);
    }

    
    public function editMcat(Request $request)
    {
        $request->validate([
            'mcat_id'    => 'required|exists:mcategories,mcat_id',
            'mcat_name'  => 'required|string|max:50',
        ]);

        $cat = Mcategory::find($request->mcat_id);
        $cat->mcat_name = $request->mcat_name;
        $cat->save();

        return response()->json(['status' => true]);
    }


    // Sub-Category code below
    public function mcatsubList()
    {
        return view('admin.mcategory.msubcatlist');
    }
    
    public function mcatsubVlist()
    {
        $subs = Msubcategory::with(['category:mcat_id,mcat_name'])->get();
        return response()->json([
            'status' => true,
            'msubcats' => $subs,
        ],200);
    }

    public function productsVlist()
    {
        $products = Mproduct::where('status','=','Active')->select('mproduct_id','mproduct_title','mproduct_image')->get();
        return response()->json([
            'status' => true,
            'products'=>$products,
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
        /* --------------------------------------------------------- 1️⃣  VALIDATE */
        $validated = $request->validate([
            'mcat_id'    => ['required', 'integer', 'exists:mcategories,mcat_id'],
            'subcatname' => ['required', 'string', 'max:120',],
            'subcattag'  => ['nullable', 'string', 'max:120'],
            'publish_to' => ['required'],
            'mcattype'   => ['required'],
            'image'      => ['required', 'image', 'max:2048'],

            'product_ids'      => ['nullable','string'],  
            'condition_logic'  => ['nullable'],
            'conditions'       => ['nullable','string']    
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
            'msubcat_publish'=> $validated['publish_to'],
            'msubcat_image'  => $subcatimagepath,
            'msubcat_type'   => $validated['mcattype'],
            'product_ids'   => $ids,
        ]);

        
        if ($validated['mcattype'] === 'smart') {

            $logic = $validated['condition_logic'] ?? 'all';
            $rows  = json_decode($validated['conditions'] ?? '[]', true);

            foreach ($rows as $row) {
                if (
                    empty($row['field_id'])   ||
                    empty($row['query_id'])   ||
                    !array_key_exists('value', $row)   
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
      
    public function editMsubcat(Request $request)
    {
        
    }

    // Collection Routes
    

    

    public function addMcoll(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => "Category added successfully",
        ]);
    }



}
