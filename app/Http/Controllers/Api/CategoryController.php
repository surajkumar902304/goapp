<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Models\Mcategory;
use App\Models\Msubcategory;
use App\Models\Wishlist;
use App\Models\Mcollection_auto;
use App\Models\Mproduct;
use App\Models\Mtag;
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
    /* ---------- 1. setup ---------- */
    $user       = $request->user();
    $needle     = mb_strtolower(trim($request->query('search', '')));
    $brandIds   = $request->query('mbrand_id');
    $brandIds   = $brandIds ? explode(',', $brandIds) : null;

    $wishlistVariantIds = $user
        ? Wishlist::where('user_id', $user->id)->pluck('mvariant_id')->toArray()
        : [];

    /* ---------- 2. load tree ---------- */
    $mainCats = MainCategory::with([
        'categories' => fn ($q) => $q->with([
            'subcategories' => fn ($q2) =>
                $q2->whereJsonContains('msubcat_publish', 'Online Store')
        ])
    ])
    ->orderBy('main_mcat_position')
    ->get();

    /* ---------- 3. attach products ---------- */
    $mainCats->each(fn ($main) =>
        $main->categories->each(fn ($cat) =>
            $cat->subcategories->each(fn ($sub) =>
                $sub->setRelation(
                    'products',
                    $this->buildProductsForSub($sub, $brandIds, $wishlistVariantIds)
                )
            )
        )
    );

    /* ---------- 4. trivial returns ---------- */
    if ($needle === '' && !$brandIds) {
        return $this->jsonResponse($mainCats);          // everything
    }

    if ($needle === '' && $brandIds) {                  // brand-only filter
        $mainCats = $mainCats->map(function ($main) {
            $cats = $main->categories->map(function ($cat) {
                $subs = $cat->subcategories
                              ->filter(fn ($s) => $s->products->isNotEmpty())
                              ->values();
                $cat->setRelation('subcategories', $subs);
                return $subs->isNotEmpty() ? $cat : null;
            })->filter()->values();

            $main->setRelation('categories', $cats);
            return $cats->isNotEmpty() ? $main : null;
        })->filter()->values();

        return $this->jsonResponse($mainCats);
    }

    /* ---------- 5. search (with / without brandIds) ---------- */
    $mainCats = $mainCats->map(function ($main) use ($needle) {

        /* ── does the MAIN-CATEGORY name itself match? ── */
        $mainNameMatches = str_contains(
            mb_strtolower($main->main_mcat_name),
            $needle
        );

        /* ── drill into categories either way ── */
        $cats = $main->categories->map(function ($cat) use ($needle, $mainNameMatches) {

            /* when main-category already matched, we can skip
               deep name-matching and only keep sub-cats that
               actually have products                                                    */
            if ($mainNameMatches) {
                $subs = $cat->subcategories
                             ->filter(fn ($s) => $s->products->isNotEmpty())
                             ->values();
                $cat->setRelation('subcategories', $subs);
                return $subs->isNotEmpty() ? $cat : null;
            }

            /* otherwise fall back to the detailed matching logic */
            $subs = $cat->subcategories->map(function ($sub) use ($needle) {

                // match sub-cat name
                if (str_contains(mb_strtolower($sub->msubcat_name), $needle)) {
                    return $sub->products->isNotEmpty() ? $sub : null;
                }

                // match individual product titles
                $matched = $sub->products->filter(fn ($p) =>
                    str_contains(mb_strtolower($p['mproduct_title']), $needle)
                );

                if ($matched->isNotEmpty()) {
                    $sub->setRelation('products', $matched->values());
                    return $sub;
                }
                return null;
            })->filter()->values();

            // if CATEGORY name matches, keep every sub that still has products
            if (str_contains(mb_strtolower($cat->mcat_name), $needle)) {
                $subs = $cat->subcategories
                             ->filter(fn ($s) => $s->products->isNotEmpty())
                             ->values();
            }

            $cat->setRelation('subcategories', $subs);
            return $subs->isNotEmpty() ? $cat : null;
        })->filter()->values();

        $main->setRelation('categories', $cats);

        return $cats->isNotEmpty() ? $main : null;
    })->filter()->values();

    return $this->jsonResponse($mainCats);
}



    private function buildProductsForSub($sub, ?array $brandIds = null, array $wishlistVariantIds = [])
    {
        $allTags = Mtag::select('mtag_id', 'mtag_name')->get()->keyBy('mtag_id');

        // ───── Load manual products ─────
        $manualProducts = collect();
        if (!empty($sub->product_ids)) {
            $manualQuery = Mproduct::with([
                'type:mproduct_type_id,mproduct_type_name',
                'brand:mbrand_id,mbrand_name',
                'mvariantsApi.mvariantDetail',
                'mvariantsApi.mstock',
                'mvariantsApi.productoffer',
            ])
            ->whereIn('mproduct_id', $sub->product_ids)
            ->where('status', 'Active')
            ->whereJsonContains('saleschannel', 'Online Store');

            if ($brandIds) {
                $manualQuery->whereIn('mbrand_id', $brandIds);
            }

            $manualProducts = $manualQuery->get();
        }

        // ───── Load smart products ─────
        $smartProducts = collect();
        if ($sub->msubcat_type === 'smart') {
            $smartProducts = $this->getSmartCollectionProducts($sub, $brandIds);
        }

        // Merge both collections
        $products = $manualProducts->merge($smartProducts)->unique('mproduct_id')->sortByDesc('mproduct_id');

        // Load rules (applies only if smart)
        $rules = Mcollection_auto::where('msubcat_id', $sub->msubcat_id)
            ->join('fields', 'fields.field_id', '=', 'mcollection_autos.field_id')
            ->join('queries', 'queries.query_id', '=', 'mcollection_autos.query_id')
            ->select('fields.field_name', 'queries.query_name', 'mcollection_autos.value')
            ->get();

        $logic = $sub->logical_operator === 'any' ? 'any' : 'all';

        // ───── Flatten products into variant-wise rows ─────
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
                    ->map(fn($id) => $allTags[$id]->mtag_name ?? null)
                    ->filter()->values()->toArray(),
            ];

            foreach ($p->mvariants as $v) {
                $inWishlist = in_array($v->mvariant_id, $wishlistVariantIds, true);
                // if ((int)$v->quantity <= 0) {
                //     continue;
                // }
                $row = array_merge($base, [
                    'mvariant_id'       => $v->mvariant_id,
                    'sku'               => $v->sku,
                    'image'             => $v->mvariant_image,
                    'price'             => $v->price,
                    'quantity'          => $v->quantity,
                    'compare_price'     => $v->compare_price,
                    'cost_price'        => $v->cost_price,
                    'taxable'           => $v->taxable,
                    'barcode'           => $v->barcode,
                    'options'           => json_decode($v->options, true),
                    'option_value'      => json_decode($v->option_value, true),
                    'mlocation_id'      => $v->mlocation_id,
                    'product_deal_tag'  => optional($v->productoffer)->product_deal_tag,
                    'product_offer'     => optional($v->productoffer)->product_offer,
                    'user_info_wishlist' => $inWishlist,
                ]);

                if ($rules->isEmpty()) {
                    $flat->push($row);
                    continue;
                }

                $results = $rules->map(fn($r) => $this->variantMatchesRule($row, $r))->all();

                $keep = $logic === 'all'
                    ? !in_array(false, $results, true)
                    : in_array(true,  $results, true);

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
   
        $rules = Mcollection_auto::where('msubcat_id', $sub->msubcat_id)->get();

        $logic = $sub->logical_operator === 'any' ? 'orWhere' : 'where';

        $query = Mproduct::where('status','Active')
                ->whereJsonContains('saleschannel','Online Store');

        if ($brandIds) {
            $query->whereIn('mbrand_id',$brandIds);
        }

        foreach ($rules as $r) {
            $val = $r->value;
            $field = $r->field_name;  

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
            'main_categories' => $payload,
        ]);
    }
}
