<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mcategory;
use App\Models\Wishlist;
use App\Models\Mcollection_auto;
use App\Models\Mproduct;
use Illuminate\Http\Request;

/**
 *  /api/categories
 *  ---------------
 *  Returns a Category → Sub‑category → Product tree.
 *  Optional filters:
 *    • ?mbrand_id=1,3,7   (comma‑list)
 *    • ?search=keyword    (case‑insensitive)
 */
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $wishlistProductIds = $user
            ? Wishlist::where('user_id', $user->id)
                      ->pluck('mproduct_id')
                      ->toArray()
            : [];

        $needle   = mb_strtolower(trim($request->query('search', '')));
        $brandIds = $request->query('mbrand_id');
        $brandIds = $brandIds ? explode(',', $brandIds) : null;

        $cats = Mcategory::with(['subcategories' => fn($q) => $q->select('*')])
                         ->get();

        $cats->each(fn($cat) =>
            $cat->subcategories->each(fn($sub) =>
                $sub->setRelation('products', $this->buildProductsForSub($sub, $brandIds, $wishlistProductIds)))
        );

        if ($needle === '' && !$brandIds) {
            return $this->jsonResponse($cats);
        }

        if ($needle === '' && $brandIds) {
            $cats = $cats->map(function($cat) {
                $subs = $cat->subcategories
                            ->filter(fn($s) => $s->products->isNotEmpty())
                            ->values();
                $cat->setRelation('subcategories', $subs);
                return $subs->isNotEmpty() ? $cat : null;
            })->filter()->values();

            return $this->jsonResponse($cats);
        }

        $cats = $cats->map(function($cat) use ($needle) {
            if (str_contains(mb_strtolower($cat->mcat_name), $needle)) {
                return $cat;
            }

            $subs = $cat->subcategories->map(function($sub) use ($needle) {
                if (str_contains(mb_strtolower($sub->msubcat_name), $needle)) {
                    return $sub;
                }
                $matched = $sub->products->filter(fn($p) =>
                    str_contains(mb_strtolower($p['mproduct_title']), $needle)
                );
                if ($matched->isNotEmpty()) {
                    $sub->setRelation('products', $matched->values());
                    return $sub;
                }
                return null;
            })->filter()->values();

            $cat->setRelation('subcategories', $subs);
            return $subs->isNotEmpty() ? $cat : null;
        })->filter()->values();

        return $this->jsonResponse($cats);
    }

    private function buildProductsForSub($sub, ?array $brandIds = null, array $wishlistProductIds = [])
    {
        if ($sub->msubcat_type === 'manual') {
            $query = Mproduct::with([
                        'type:mproduct_type_id,mproduct_type_name',
                        'brand:mbrand_id,mbrand_name',
                        'mvariantsApi' => fn($q) => $q
                            ->join('mvariant_details','mvariant_details.mvariant_id','=','mvariants.mvariant_id')
                            ->join('mstocks','mstocks.mvariant_id','=','mvariants.mvariant_id')
                            ->select(
                                'mvariants.mvariant_id','mvariants.sku','mvariants.mvariant_image',
                                'mvariants.price','mvariants.compare_price','mvariants.cost_price',
                                'mvariants.taxable','mvariants.barcode',
                                'mvariant_details.options','mvariant_details.option_value',
                                'mstocks.quantity','mstocks.mlocation_id'
                            )
                    ])
                    ->whereIn('mproduct_id',$sub->product_ids ?? [])
                    ->where('status','Active');

            if ($brandIds) {
                $query->whereIn('mbrand_id',$brandIds);
            }

            $products = $query->get();
        } else {
            $products = $this->getSmartCollectionProducts($sub, $brandIds);
        }

        $flat = collect();
        foreach ($products as $p) {
            $inWishlist = in_array($p->mproduct_id, $wishlistProductIds, true);
            foreach ($p->mvariants as $v) {
                $flat->push([
                    'mproduct_id'           => $p->mproduct_id,
                    'mproduct_title'        => $p->mproduct_title,
                    'mproduct_image'        => $p->mproduct_image,
                    'mproduct_slug'         => $p->mproduct_slug,
                    'mproduct_desc'         => $p->mproduct_desc,
                    'status'                => $p->status,
                    'saleschannel'          => $p->saleschannel,
                    'product_deal_tag'      => $p->product_deal_tag,
                    'product_offer'         => $p->product_offer,
                    'product_type'          => optional($p->type)->mproduct_type_name,
                    'brand_name'            => optional($p->brand)->mbrand_name,
                    'mvariant_id'           => $v->mvariant_id,
                    'sku'                   => $v->sku,
                    'image'                 => $v->mvariant_image,
                    'price'                 => $v->price,
                    'compare_price'         => $v->compare_price,
                    'cost_price'            => $v->cost_price,
                    'taxable'               => $v->taxable,
                    'barcode'               => $v->barcode,
                    'options'               => $v->options,
                    'option_value'          => $v->option_value,
                    'quantity'              => $v->quantity,
                    'mlocation_id'          => $v->mlocation_id,
                    'user_info_wishlist'    => $inWishlist,
                ]);
            }
        }

        return $flat->values();
    }

    private function getSmartCollectionProducts($sub, ?array $brandIds = null)
    {
        $rules = Mcollection_auto::query()
            ->where('msubcat_id',$sub->msubcat_id)
            ->join('fields','fields.field_id','=','mcollection_autos.field_id')
            ->join('queries','queries.query_id','=','mcollection_autos.query_id')
            ->select('fields.field_name','queries.query_name','mcollection_autos.value')
            ->get();

        $query = Mproduct::where('status','Active');

        foreach ($rules as $r) {
            [$field,$op,$val] = [$r->field_name,$r->query_name,$r->value];
            switch ($field) {
                case 'Title':
                    $query = $this->applyTextRule($query,'mproduct_title',$op,$val);           break;
                case 'Brand':
                    $query->whereHas('brand',fn($q)=>$this->applyTextRule($q,'mbrand_name',$op,$val)); break;
                case 'Type':
                    $query->whereHas('type', fn($q)=>$this->applyTextRule($q,'mproduct_type_name',$op,$val)); break;
                case 'Price':
                    $query->whereHas('mvariants',fn($q)=>$this->applyNumericRule($q,'price',$op,$val));       break;
                case 'Inventory stock':
                    $query->whereHas('mvariants',fn($q)=>$this->applyNumericRule($q,'quantity',$op,$val));    break;
            }
        }

        if ($brandIds) {
            $query->whereIn('mbrand_id',$brandIds);
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

    private function applyTextRule($q,string $col,string $op,string $val)
    {
        return match($op){
            'is equal to'        => $q->where($col,$val),
            'is not equal to'    => $q->where($col,'!=',$val),
            'contains'           => $q->where($col,'like',"%$val%"),
            'does not contains'  => $q->where($col,'not like',"%$val%"),
            'starts with'        => $q->where($col,'like',"$val%"),
            'ends with'          => $q->where($col,'like',"%$val"),
            default              => $q,
        };
    }
    private function applyNumericRule($q,string $col,string $op,$val)
    {
        return match($op){
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
