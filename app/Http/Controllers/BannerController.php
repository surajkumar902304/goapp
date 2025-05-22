<?php

namespace App\Http\Controllers;

use App\Models\Browsebanner;
use App\Models\MainCategory;
use App\Models\Mcategory;
use App\Models\Mcollection_auto;
use App\Models\Mproduct;
use App\Models\Mtag;
use App\Models\Field;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Query;

class BannerController extends Controller
{
    public function browseBannerList()
    {
       return view('admin.banner.browsebanner');
    }

    public function browseBannerVlist()
    {
        $banners = Browsebanner::with([
            'category:mcat_id,mcat_name',
            'subcategory:msubcat_id,msubcat_name',
            'product:mproduct_id,mproduct_title'
        ])
        ->orderBy('browsebanner_position')
        ->get()
        ->map(function ($b) {
            return [
                'browsebanner_id'      => $b->browsebanner_id,
                'browsebanner_name'    => $b->browsebanner_name,
                'browsebanner_image'   => $b->browsebanner_image,
                'browsebanner_position'=> $b->browsebanner_position,

                /* foreign‑keys */
                'main_mcat_id'    => $b->main_mcat_id,
                'mcat_id'    => $b->mcat_id,
                'msubcat_id' => $b->msubcat_id,
                'mproduct_id'=> $b->mproduct_id,

                /* human‑readable names */
                'main_mcat_name'     => optional($b->category)->main_mcat_name,
                'mcat_name'     => optional($b->category)->mcat_name,
                'msubcat_name'  => optional($b->subcategory)->msubcat_name,
                'mproduct_title'=> optional($b->product)->mproduct_title,
            ];
        });

        return response()->json([
        'status'       => true,
        'browsebanner' => $banners
        ], 200);
    }


    public function reorder(Request $request)
    {
        foreach ($request->all() as $item) {
            Browsebanner::where('browsebanner_id', $item['id'])
                ->update(['browsebanner_position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }


    public function addBrowseBanner(Request $request)
    {
        $request->validate([
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'browsebanner_name'  => 'required|string|max:50',
            'browsebanner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $banner_imgpath = null;
        if ($request->hasFile('browsebanner_image')) {
            $image  = $request->file('browsebanner_image');
            $filename = 'browsebanner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            $banner_imgpath      = 'goapp/images/browsebanner/' . $filename;
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());
        }

        $browsebanner  = new Browsebanner();
        $browsebanner->main_mcat_id    = $request->main_mcat_id;
        $browsebanner->mcat_id    = $request->mcat_id;
        $browsebanner->msubcat_id    = $request->msubcat_id;
        $browsebanner->mproduct_id    = $request->mproduct_id;
        $browsebanner->browsebanner_name    = $request->browsebanner_name;
        $browsebanner->browsebanner_image   = $banner_imgpath;
        $browsebanner->browsebanner_position = Browsebanner::max('browsebanner_position') + 1;
        $browsebanner->save();

        return response()->json(['status' => true]);
    }

    public function editBrowseBanner(Request $request)
    {
        $request->validate([
            'browsebanner_id'    => 'required|exists:browsebanners,browsebanner_id',
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'browsebanner_name'  => 'required|string|max:255',
            'browsebanner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $browsebanner = Browsebanner::find($request->browsebanner_id);
        $browsebanner->main_mcat_id  = $request->main_mcat_id;
        $browsebanner->mcat_id  = $request->mcat_id;
        $browsebanner->msubcat_id  = $request->msubcat_id;
        $browsebanner->mproduct_id  = $request->mproduct_id;
        $browsebanner->browsebanner_name  = $request->browsebanner_name;
        $banner_imgpath = $browsebanner->browsebanner_image;

        if ($request->hasFile('browsebanner_image')) {
            if (!empty($banner_imgpath) && Storage::disk('s3')->exists($banner_imgpath)) {
                Storage::disk('s3')->delete($banner_imgpath);
            }
            $image = $request->file('browsebanner_image');
            $filename = 'browsebanner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            
            $banner_imgpath      = "goapp/images/browsebanner/$filename";
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());

            $browsebanner->browsebanner_image = $banner_imgpath;
        }

        $browsebanner->save();

        return response()->json(['status' => true]);
    }

    public function deleteBrowseBanner(Request $request)
    {
        $request->validate([
            'browsebanner_id' => 'required|exists:browsebanners,browsebanner_id',
        ]);

        try {
            $browsebanner = Browsebanner::findOrFail($request->browsebanner_id);

            if ($browsebanner->browsebanner_image && Storage::disk('s3')->exists($browsebanner->browsebanner_image)) {
                Storage::disk('s3')->delete($browsebanner->browsebanner_image);
            }

            $browsebanner->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // Category main API

    public function index(Request $request)
    {
        // Load the full structure first
        $mainCategories = MainCategory::with([
            'categories' => function ($q) {
                $q->select('*')->with([
                    'subcategories' 
                ]);
            }
        ])->get();

        // Attach filtered products to subcategories
        $mainCategories->each(function ($main) {
            $main->categories->each(function ($cat) {
                $cat->subcategories->each(function ($sub) {
                    $sub->setRelation('products', collect($this->buildProductsForSub($sub)));
                });

                // Filter out subcategories without products
                $cat->setRelation('subcategories', $cat->subcategories->filter(
                    fn($sub) => $sub->products->isNotEmpty()
                )->values());
            });

            // Filter out categories with no subcategories left
            $main->setRelation('categories', $main->categories->filter(
                fn($cat) => $cat->subcategories->isNotEmpty()
            )->values());
        });

        // Final filter to only keep main categories that still have categories
        $filtered = $mainCategories->filter(
            fn($main) => $main->categories->isNotEmpty()
        )->values();

        return $this->jsonResponse($filtered);
    }

    /* ------------------------------------------------------------------ */
    /*  Build list of **matching** variants for ONE smart sub-category    */
    /* ------------------------------------------------------------------ */
    private function buildProductsForSub($sub)
    {
        $products = $this->getSmartCollectionProducts($sub);

        $allTags = Mtag::select('mtag_id', 'mtag_name')->get()->keyBy('mtag_id');

        $rules = Mcollection_auto::where('msubcat_id', $sub->msubcat_id)
                    ->join('fields',  'fields.field_id',  '=', 'mcollection_autos.field_id')
                    ->join('queries', 'queries.query_id', '=', 'mcollection_autos.query_id')
                    ->select('fields.field_name','queries.query_name','mcollection_autos.value')
                    ->get();

        $logic = $sub->logical_operator === 'any' ? 'any' : 'all';

        $flat = collect();

        foreach ($products as $p) {
            $base = [
                'mproduct_id'    => $p->mproduct_id,
                'mproduct_title' => $p->mproduct_title,
                'mproduct_image' => $p->mproduct_image,
                'mproduct_slug'  => $p->mproduct_slug,
                'mproduct_desc'  => $p->mproduct_desc,
                'status'         => $p->status,
                'saleschannel'   => $p->saleschannel,
                'brand_id'       => $p->mbrand_id,
                'brand_name'     => optional($p->brand)->mbrand_name,
                'type_id'        => $p->mproduct_type_id,
                'product_type'   => optional($p->type)->mproduct_type_name,
                'tag_ids'        => $p->mtags ?? [],
                'tag_names'      => collect($p->mtags ?? [])
                                        ->map(fn($id)=>$allTags[$id]->mtag_name??null)
                                        ->filter()->values()->toArray(),
            ];

            foreach ($p->mvariants as $v) {
                $row = array_merge($base, [
                    'mvariant_id'   => $v->mvariant_id,
                    'sku'           => $v->sku,
                    'image'         => $v->mvariant_image,
                    'price'         => $v->price,
                    'quantity'      => $v->quantity,
                    'compare_price' => $v->compare_price,
                    'cost_price'    => $v->cost_price,
                    'taxable'       => $v->taxable,
                    'barcode'       => $v->barcode,
                    'options'       => $v->options,
                    'option_value'  => $v->option_value,
                    'mlocation_id'  => $v->mlocation_id,
                ]);

                if ($rules->isEmpty()) { 
                    $flat->push($row);
                    continue;
                }

                $results = $rules->map(fn($r)=>$this->variantMatchesRule($row,$r))->all();

                $keep = $logic === 'all'
                      ? !in_array(false, $results, true)
                      :  in_array(true,  $results, true);

                if ($keep) $flat->push($row);
            }
        }
        return $flat->values();
    }

    /* ------------------------------------------------------------------ */
    /*  ONE rule ↔ ONE variant                                            */
    /* ------------------------------------------------------------------ */
    private function variantMatchesRule(array $row, $rule): bool
    {
        $field = $rule->field_name;
        $op    = $rule->query_name;
        $val   = mb_strtolower((string)$rule->value);

        $actual = match ($field) {
            'Title'            => mb_strtolower($row['mproduct_title']),
            'Brand'            => (string)$row['brand_id'],
            'Type'             => (string)$row['type_id'],
            'Tag'              => array_map('strval', $row['tag_ids']),
            'Price'            => (float)$row['price'],
            'Inventory stock'  => (int)  $row['quantity'],
            default            => null,
        };

        if (in_array($field, ['Price','Inventory stock'], true)) {
            return match ($op) {
                'is equal to'     => $actual == (float)$val,
                'is not equal to' => $actual != (float)$val,
                'greater than'    => $actual >  (float)$val,
                'less than'       => $actual <  (float)$val,
                default           => false,
            };
        }

        if (in_array($field, ['Brand','Type'], true)) {
            return match ($op) {
                'is equal to'     => $actual === $val,
                'is not equal to' => $actual !== $val,
                default           => false,
            };
        }

        if ($field === 'Tag') {
            return match ($op) {
                'is equal to'              => in_array($val, $actual),
                default                    => false,
            };
        }

        if ($field === 'Title') {
            return match ($op) {
                'is equal to'        => $actual === $val,
                'is not equal to'    => $actual !== $val,
                'contains'           => str_contains($actual,$val),
                'does not contains'  => !str_contains($actual,$val),
                'starts with'        => str_starts_with($actual,$val),
                'ends with'          => str_ends_with($actual,$val),
                default              => false,
            };
        }
        return false;
    }

    /* ------------------------------------------------------------------ */
    /*  SQL helper: fetch candidates for ONE smart sub-category           */
    /* ------------------------------------------------------------------ */
    private function getSmartCollectionProducts($sub, ?array $brandIds = null)
    {
        /* COLLECT field→op→value triplets */
        $rules = Mcollection_auto::where('msubcat_id', $sub->msubcat_id)->get();

        $logic = $sub->logical_operator === 'any' ? 'orWhere' : 'where';

        $query = Mproduct::where('status','Active')
                         ->whereJsonContains('saleschannel','Online Store');

        if ($brandIds) {
            $query->whereIn('mbrand_id',$brandIds);
        }

        /* apply only the rules that can be expressed in SQL
           (Title, Brand, Type, Tag, Price, Inventory stock)            */
        foreach ($rules as $r) {
            $val = $r->value;
            $field = $r->field_name;       // use the text directly

            switch ($field) {
                case 'Title':
                    $query->$logic('mproduct_title', 'like', "%$val%");
                    break;

                case 'Brand':
                    $query->$logic('mbrand_id', intval($val));
                    break;

                case 'Type':
                    $query->$logic('mproduct_type_id', intval($val));
                    break;

                case 'Tag':
                    $query->$logic(fn($q) => $q->whereJsonContains('mtags', intval($val)));
                    break;

                case 'Price':
                    $query->$logic(function ($q) use ($r, $val) {
                        $q->whereHas('mvariants', fn($v) =>
                            $this->applyNumericRule($v, 'price', $r->query_name, $val)
                        );
                    });
                    break;

                case 'Inventory stock':
                    $query->$logic(function ($q) use ($r, $val) {
                        $q->whereHas('mvariants', function ($v) use ($r, $val) {
                            $v->join('mstocks', 'mstocks.mvariant_id', '=', 'mvariants.mvariant_id');
                            $this->applyNumericRule($v, 'mstocks.quantity', $r->query_name, $val);
                        });
                    });
                    break;
            }
        }


        return $query->with([
            'type:mproduct_type_id,mproduct_type_name',
            'brand:mbrand_id,mbrand_name',
            'mvariantsApi'      => fn($q)=>$q
                ->join('mvariant_details','mvariant_details.mvariant_id','=','mvariants.mvariant_id')
                ->join('mstocks','mstocks.mvariant_id','=','mvariants.mvariant_id')
                ->select(
                    'mvariants.mvariant_id','mvariants.sku','mvariants.mvariant_image',
                    'mvariants.price','mvariants.compare_price','mvariants.cost_price',
                    'mvariants.taxable','mvariants.barcode',
                    'mvariant_details.options','mvariant_details.option_value',
                    'mstocks.quantity','mstocks.mlocation_id'
                )
        ])->get();
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers already used elsewhere (numeric + jsonResponse)           */
    /* ------------------------------------------------------------------ */
    private function applyNumericRule($q,string $col,string $op,$val)
    {
        return match ($op) {
            'is equal to'     => $q->where($col,$val),
            'is not equal to' => $q->where($col,'!=',$val),
            'greater than'    => $q->where($col,'>',$val),
            'less than'       => $q->where($col,'<',$val),
            default           => $q,
        };
    }

    private function jsonResponse($payload)
    {
        return response()->json([
            'status'     => true,
            'message'    => 'Fetch all Categories Successfully',
            'cdnURL'     => config('cdn.url'),
            'categories' => $payload,
        ]);
    }
}
