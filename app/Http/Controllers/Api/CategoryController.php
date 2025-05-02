<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mcategory;
use App\Models\Mcollection_auto;
use App\Models\Mproduct;
use Illuminate\Http\Request;

/**
 *  Category → Sub‑category → Product tree (searchable)
 */
class CategoryController extends Controller
{
    /* ------------------------------------------------------------------ */
    /*  GET /api/categories[?search=xxx]                                  */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $needle = mb_strtolower(trim($request->query('search', '')));

        $cats = Mcategory::with(['subcategories' => fn ($q) => $q->select('*')])
                ->get();

        $cats->each(fn ($cat) =>
            $cat->subcategories->each(fn ($sub) =>
                $sub->setRelation('products', $this->buildProductsForSub($sub)))
        );

        if ($needle === '') {
            return $this->jsonResponse($cats);
        }

        $filtered = $cats->filter(function ($cat) use ($needle) {

            $categoryHit = str_contains(mb_strtolower($cat->mcat_name), $needle);
            if ($categoryHit) {
                return true;   
            }
            $subFiltered = $cat->subcategories->filter(function ($sub) use ($needle) {

                $subHit  = str_contains(mb_strtolower($sub->msubcat_name), $needle);

                $matchedProducts = $sub->products->filter(function ($p) use ($needle) {
                    return str_contains(mb_strtolower($p['mproduct_title']), $needle);
                });

                if ($subHit) {
                    return true;
                }

                if ($matchedProducts->isNotEmpty()) {
                    $sub->setRelation('products', $matchedProducts->values());
                    return true;
                }

                return false;
            })->values();  

            $cat->setRelation('subcategories', $subFiltered);

            return $categoryHit || $subFiltered->isNotEmpty();
        })->values();     

        return $this->jsonResponse($filtered);

    }

    /* ------------------------------------------------------------------ */
    /*  Build product list (flattened variants) for a sub‑category        */
    /* ------------------------------------------------------------------ */
    private function buildProductsForSub($sub)
    {
        /* MANUAL sub‑categories ---------------------------------------- */
        if ($sub->msubcat_type === 'manual') {
            $products = Mproduct::with([
                'type:mproduct_type_id,mproduct_type_name',
                'brand:mbrand_id,mbrand_name',
                'mvariantsApi' => function ($q) {
                    $q->join('mvariant_details', 'mvariant_details.mvariant_id', '=', 'mvariants.mvariant_id')
                      ->join('mstocks',          'mstocks.mvariant_id',          '=', 'mvariants.mvariant_id')
                      ->select(
                          'mvariants.mvariant_id','mvariants.sku','mvariants.mvariant_image',
                          'mvariants.price','mvariants.compare_price','mvariants.cost_price',
                          'mvariants.taxable','mvariants.barcode',
                          'mvariant_details.options','mvariant_details.option_value',
                          'mstocks.quantity','mstocks.mlocation_id'
                      );
                }
            ])
            ->whereIn('mproduct_id', $sub->product_ids ?? [])
            ->where('status', 'Active')
            ->get();

        /* SMART sub‑categories ---------------------------------------- */
        } else {
            $products = $this->getSmartCollectionProducts($sub);
        }

        $flat = collect();
        foreach ($products as $p) {
            foreach ($p->mvariants as $v) {
                $flat->push([
                    'mproduct_id'    => $p->mproduct_id,
                    'mproduct_title' => $p->mproduct_title,
                    'mproduct_image' => $p->mproduct_image,
                    'mproduct_slug'  => $p->mproduct_slug,
                    'mproduct_desc'  => $p->mproduct_desc,
                    'status'         => $p->status,
                    'saleschannel'   => $p->saleschannel,
                    'product_type'   => optional($p->type)->mproduct_type_name,
                    'brand_name'     => optional($p->brand)->mbrand_name,

                    'mvariant_id'   => $v->mvariant_id,
                    'sku'           => $v->sku,
                    'image'         => $v->mvariant_image,
                    'price'         => $v->price,
                    'compare_price' => $v->compare_price,
                    'cost_price'    => $v->cost_price,
                    'taxable'       => $v->taxable,
                    'barcode'       => $v->barcode,
                    'options'       => $v->options,
                    'option_value'  => $v->option_value,
                    'quantity'      => $v->quantity,
                    'mlocation_id'  => $v->mlocation_id,
                ]);
            }
        }

        return $flat->values();
    }

    /* ------------------------------------------------------------------ */
    /*  SMART‑collection builder (same logic you already had)             */
    /* ------------------------------------------------------------------ */
    private function getSmartCollectionProducts($sub)
    {
        $rules = Mcollection_auto::query()
            ->where('msubcat_id', $sub->msubcat_id)
            ->join('fields',  'fields.field_id',  '=', 'mcollection_autos.field_id')
            ->join('queries', 'queries.query_id', '=', 'mcollection_autos.query_id')
            ->select('fields.field_name','queries.query_name','mcollection_autos.value')
            ->get();

        $query = Mproduct::where('status', 'Active');

        foreach ($rules as $r) {
            [$field,$op,$val] = [$r->field_name,$r->query_name,$r->value];
            switch ($field) {
                case 'Title':
                    $query = $this->applyTextRule($query, 'mproduct_title', $op, $val); break;
                case 'Brand':
                    $query->whereHas('brand', fn ($q) => $this->applyTextRule($q,'mbrand_name',$op,$val)); break;
                case 'Type':
                    $query->whereHas('type',  fn ($q) => $this->applyTextRule($q,'mproduct_type_name',$op,$val)); break;
                case 'Price':
                    $query->whereHas('mvariants', fn ($q) => $this->applyNumericRule($q,'price',$op,$val)); break;
                case 'Inventory stock':
                    $query->whereHas('mvariants', fn ($q) => $this->applyNumericRule($q,'quantity',$op,$val)); break;
            }
        }

        return $query->with([
            'type:mproduct_type_id,mproduct_type_name',
            'brand:mbrand_id,mbrand_name',
            'mvariantsApi' => function ($q) {
                $q->join('mvariant_details','mvariant_details.mvariant_id','=','mvariants.mvariant_id')
                  ->join('mstocks','mstocks.mvariant_id','=','mvariants.mvariant_id')
                  ->select(
                      'mvariants.mvariant_id','mvariants.sku','mvariants.mvariant_image',
                      'mvariants.price','mvariants.compare_price','mvariants.cost_price',
                      'mvariants.taxable','mvariants.barcode',
                      'mvariant_details.options','mvariant_details.option_value',
                      'mstocks.quantity','mstocks.mlocation_id'
                  );
            }
        ])->get();
    }

    /* ------------------------------------------------------------------ */
    /*  tiny helpers for rule building                                    */
    /* ------------------------------------------------------------------ */
    private function applyTextRule($q, string $col, string $op, string $val)
    {
        return match ($op) {
            'is equal to'        => $q->where($col, $val),
            'is not equal to'    => $q->where($col, '!=', $val),
            'contains'           => $q->where($col, 'like', "%$val%"),
            'does not contains'  => $q->where($col, 'not like', "%$val%"),
            'starts with'        => $q->where($col, 'like', "$val%"),
            'ends with'          => $q->where($col, 'like', "%$val"),
            default              => $q,
        };
    }

    private function applyNumericRule($q, string $col, string $op, $val)
    {
        return match ($op) {
            'is equal to'     => $q->where($col, $val),
            'is not equal to' => $q->where($col, '!=', $val),
            'greater than'    => $q->where($col, '>',  $val),
            'less than'       => $q->where($col, '<',  $val),
            default           => $q,
        };
    }

    /* ------------------------------------------------------------------ */
    /*  common JSON formatter                                             */
    /* ------------------------------------------------------------------ */
    private function jsonResponse($payload)
    {
        return response()->json([
            'status'     => true,
            'message'    => 'Fetch all Categories Successfully',
            'cdnURL'     => config('cdn.url'),   // reads AWS_CDN_URL from .env
            'categories' => $payload,
        ]);
    }
}
