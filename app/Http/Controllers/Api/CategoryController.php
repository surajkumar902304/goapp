<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mcategory;
use App\Models\Mcollection_auto;
use App\Models\Mproduct;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    /* ① root categories + eager subcats */
    $cats = Mcategory::with(['subcategories' => function ($q) {
        $q->select('*');          // sub-cats
    }])->get();

    /* ② हर subcat के type के हिसाब से extra डेटा जोड़ें */
    $cats->each(function ($cat) {
        $cat->subcategories->each(function ($sub) {
    
            /* ────────── MANUAL collection ────────── */
            if ($sub->msubcat_type === 'manual') {
    
                $products = Mproduct::with([
                    /* type / brand वही जैसे पहले */
                    'type:mproduct_type_id,mproduct_type_name',
                    'brand:mbrand_id,mbrand_name',
            
                    /* variants + stock + option values */
                    'mvariantsApi' => function ($q) {
                        $q->join('mvariant_details', 'mvariant_details.mvariant_id','=','mvariants.mvariant_id')
                          ->join('mstocks',          'mstocks.mvariant_id',         '=','mvariants.mvariant_id')
                          ->select(
                              'mvariants.mvariant_id',
                              'mvariants.sku',
                              'mvariants.mvariant_image',
                              'mvariants.price',
                              'mvariants.compare_price',
                              'mvariants.cost_price',
                              'mvariants.taxable',
                              'mvariants.barcode',
                              'mvariant_details.options',
                              'mvariant_details.option_value',
                              'mstocks.quantity',
                              'mstocks.mlocation_id'
                          );
                    }
                ])
                ->whereIn('mproduct_id', $sub->product_ids ?? [])
                ->get();
                
                $flattened = collect();         // खाली कलेक्शन

foreach ($products as $p) {
    foreach ($p->mvariants as $v) {
        $flattened->push([
            'mproduct_id'    => $p->mproduct_id,
            'mproduct_title' => $p->mproduct_title,
            'mproduct_image' => $p->mproduct_image,
            'mproduct_slug'  => $p->mproduct_slug,
            'mproduct_desc'  => $p->mproduct_desc,
            'status'         => $p->status,
            'saleschannel'   => $p->saleschannel,
            'product_type'   => $p->type?->mproduct_type_name,
            'brand_name'     => $p->brand?->mbrand_name,

            /* variant-specific कॉलम सीधा इसी लेवल पर */
            'mvariant_id'    => $v->mvariant_id,
            'sku'            => $v->sku,
            'image'          => $v->mvariant_image,
            'price'          => $v->price,
            'compare_price'  => $v->compare_price,
            'cost_price'     => $v->cost_price,
            'taxable'        => $v->taxable,
            'barcode'        => $v->barcode,
            'options'        => $v->options,
            'option_value'   => $v->option_value,
            'quantity'       => $v->quantity,
            'mlocation_id'   => $v->mlocation_id,
        ]);
    }
}
            
    
                $sub->setRelation('products', $flattened);
    
            /* ────────── SMART collection ────────── */
        } else {

            /* 1️⃣  rules + name-mapping  */
            $rules = Mcollection_auto::query()
                ->where('msubcat_id', $sub->msubcat_id)
                ->join('fields',  'fields.field_id',  '=', 'mcollection_autos.field_id')
                ->join('queries',  'queries.query_id',  '=', 'mcollection_autos.query_id')
                ->select(
                    'mcollection_autos.field_id',
                    'fields.field_name',
                    'mcollection_autos.query_id',
                    'queries.query_name',
                    'mcollection_autos.value',
                    'mcollection_autos.logical_operator'
                )
                ->get();
        
        
            /* 2️⃣  उन rules को चला कर products छाँटें
                    (यह demo सिर्फ Title / Price / Type / Brand पर काम दिखाता है;
                     आप अपनी पूरी logic चाहें तो Repository / Service में लिखें)        */
            $query = Mproduct::query();
        
            foreach ($rules as $r) {
                [$field, $op, $val] = [$r->field_name, $r->query_name, $r->value];
        
                switch ($field) {
                    /* ───────────── TEXT फ़ील्ड्स ───────────── */
                    case 'Title':
                        $query = $this->applyTextRule($query, 'mproduct_title', $op, $val);
                        break;
                
                    case 'Brand':
                        $query->whereHas('brand', function ($q) use ($op, $val) {
                            $this->applyTextRule($q, 'mbrand_name', $op, $val);
                        });
                        break;
                
                    case 'Type':
                        $query->whereHas('type', function ($q) use ($op, $val) {
                            $this->applyTextRule($q, 'mproduct_type_name', $op, $val);
                        });
                        break;
                
                    /* ───────────── NUMERIC फ़ील्ड्स (variants में) ───────────── */
                    case 'Price':
                        $query->whereHas('mvariants', function ($q) use ($op, $val) {
                            $this->applyNumericRule($q, 'price', $op, $val);
                        });
                        break;
                
                    case 'Inventory stock':
                        $query->whereHas('mvariants', function ($q) use ($op, $val) {
                            $this->applyNumericRule($q, 'quantity', $op, $val);   // quantity mstocks join से आ रहा है
                        });
                        break;
                
                    /* … अन्य फ़ील्ड्स … */
                }
                
            }
        
            /* eager-load variants / type / brand, जैसा manual वाले में किया था */
            $matchingProducts = $query->with([
                /* type / brand वही जैसे पहले */
                'type:mproduct_type_id,mproduct_type_name',
                'brand:mbrand_id,mbrand_name',
        
                /* variants + stock + option values */
                'mvariantsApi' => function ($q) {
                    $q->join('mvariant_details', 'mvariant_details.mvariant_id','=','mvariants.mvariant_id')
                      ->join('mstocks',          'mstocks.mvariant_id',         '=','mvariants.mvariant_id')
                      ->select(
                          'mvariants.mvariant_id',
                          'mvariants.sku',
                          'mvariants.mvariant_image',
                          'mvariants.price',
                          'mvariants.compare_price',
                          'mvariants.cost_price',
                          'mvariants.taxable',
                          'mvariants.barcode',
                          'mvariant_details.options',
                          'mvariant_details.option_value',
                          'mstocks.quantity',
                          'mstocks.mlocation_id'
                      );
                }
            ])
            ->get();

            $flattened = collect();

            foreach ($matchingProducts as $p) {
                foreach ($p->mvariants as $v) {
                    $flattened->push([
                        'mproduct_id'    => $p->mproduct_id,
                        'mproduct_title' => $p->mproduct_title,
                        'mproduct_image' => $p->mproduct_image,
                        'mproduct_slug'  => $p->mproduct_slug,
                        'mproduct_desc'  => $p->mproduct_desc,
                        'status'         => $p->status,
                        'saleschannel'   => $p->saleschannel,
                        'product_type'   => $p->type?->mproduct_type_name,
                        'brand_name'     => $p->brand?->mbrand_name,
            
                        /* variant-specific कॉलम सीधा इसी लेवल पर */
                        'mvariant_id'    => $v->mvariant_id,
                        'sku'            => $v->sku,
                        'image'          => $v->mvariant_image,
                        'price'          => $v->price,
                        'compare_price'  => $v->compare_price,
                        'cost_price'     => $v->cost_price,
                        'taxable'        => $v->taxable,
                        'barcode'        => $v->barcode,
                        'options'        => $v->options,
                        'option_value'   => $v->option_value,
                        'quantity'       => $v->quantity,
                        'mlocation_id'   => $v->mlocation_id,
                    ]);
                }
            }
            $sub->setRelation('products', $flattened);
        }
        });
    });
    
    

    return response()->json([
        'status'     => true,
        'categories' => $cats,
    ]);
}

/* text-based comparisons */
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

/* numeric comparisons (Price, Inventory … ) */
private function applyNumericRule($q, string $col, string $op, $val)
{
    return match ($op) {
        'is equal to'     => $q->where($col,  $val),
        'is not equal to' => $q->where($col, '!=', $val),
        'greater than'    => $q->where($col, '>',  $val),
        'less than'       => $q->where($col, '<',  $val),
        default           => $q,
    };
}

    
}
