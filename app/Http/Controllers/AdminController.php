<?php

namespace App\Http\Controllers;

use App\Models\Mbrand;
use App\Models\Mlocation;
use App\Models\Moption;
use App\Models\Mproduct;
use App\Models\Mproduct_type;
use App\Models\Mstock;
use App\Models\Mtag;
use App\Models\Mvariant;
use App\Models\Mvariant_detail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\str;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function adminlogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('admin')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->remember
        )) {
            return redirect()->route('admin.index');
        }
        else{

            return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => 'Credentials do not match our records.',
            ]);
        }
    }

    public function adminlogout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    // User
    public function userList()
    {
        return view('admin.user.adminapproval');
    }

    public function userVlist()
    {
        $users = User::get();
        return response()->json([
            'status' => true,
            'users' => $users,
        ],200);
    }

    public function updateUserApproval(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'admin_approval' => 'required|boolean'
        ]);

        $user = User::find($request->user_id);
        $user->admin_approval = $request->admin_approval;
        $user->save();

        return response()->json(['success' => true]);
    }

    // product
    public function productsList()
    {
        return view('admin.product.list');
    }

    public function productAddView()
    {
        return view('admin.product.add');
    }

    public function moptionsList()
    {
       return view('admin.moptions.list');
    }

    public function moptionsVlist()
    {
        $moptions = Moption::get();
        return response()->json([
            'status' => true,
            'moptions' => $moptions,
        ],200);
    }

    public function addMoption(Request $request)
    {
        $moption = new Moption();
        $moption->moption_name = $request->moption_name;
        $moption->save();

        return response()->json([
            'status' => true,
            'moption' => $moption,
        ]);
    }

    public function editMoption(Request $request)
    {
        $moption = Moption::Find($request->moption_id);
        $moption->moption_name = $request->moption_name;
        $moption->update();
        return response()->json([
            'status' => true,
            'moption' => $moption,
        ]);
    }

    // Brands 
    public function mbrandList()
    {
       return view('admin.brand.index');
    }

    public function mbrandVlist()
    {
        $mbrand = Mbrand::get();
        return response()->json([
            'status' => true,
            'mbrands' => $mbrand,
        ],200);
    }

    public function addBrand(Request $request)
    {
        $request->validate([
            'mbrand_name'  => 'required|string|max:50',
            'mbrand_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $bimagepath = null;
        if ($request->hasFile('mbrand_image')) {
            $image  = $request->file('mbrand_image');
            $filename = 'mbrand_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath())->resize(600, 800, function ($constraint) {
                $constraint->aspectRatio();
            });
            $bimagepath      = 'goapp/images/mbrands/' . $filename;
            Storage::disk('s3')->put($bimagepath, (string) $img->encode());
        }

        $brand                 = new Mbrand();
        $brand->mbrand_name    = $request->mbrand_name;
        $brand->mbrand_image   = $bimagepath;
        $brand->save();

        return response()->json(['status' => true]);
    }

    public function editBrand(Request $request)
    {
        $request->validate([
            'mbrand_id'    => 'required|exists:mbrands,mbrand_id',
            'mbrand_name'  => 'required|string|max:255',
            'mbrand_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $brand = Mbrand::find($request->mbrand_id);
        $brand->mbrand_name  = $request->mbrand_name;
        $bimagepath = $brand->mbrand_image;

        if ($request->hasFile('mbrand_image')) {
            if (!empty($bimagepath) && Storage::disk('s3')->exists($bimagepath)) {
                Storage::disk('s3')->delete($bimagepath);
            }
            $image = $request->file('mbrand_image');
            $filename = 'mbrand_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath())->resize(600, 800, function ($constraint) {
                $constraint->aspectRatio();
            });
            
            $bimagepath      = "goapp/images/mbrands/$filename";
            Storage::disk('s3')->put($bimagepath, (string) $img->encode());

            $brand->mbrand_image = $bimagepath;
        }

        $brand->save();

        return response()->json(['status' => true]);
    }


    public function adminProductlist()
    {
        $mproducts = Mproduct::with('mvariants')
        ->orderBy('mproduct_id', 'desc')
        ->get();

        $mptypes  = Mproduct_type::all();
        $mbrands  = Mbrand::all();
        $mtags    = Mtag::all();
        return response()->json([
            'status' => true,
            'mproducts' => $mproducts,
            'mptypes'  => $mptypes,
            'mbrands'  => $mbrands,
            'mtags'    => $mtags,
        ]);
    }

    // Product functionalty 
    public function productAddData()
    {
        $mptypes = Mproduct_type::get();
        $mbrands = Mbrand::get();
        $mtags = Mtag::get();
        $availableOptions = Moption::get();

        return response()->json([
            'status' => true,
            'ptypes'=> $mptypes,
            'brands' => $mbrands,
            'tags' => $mtags,
            'selectedOption'=> $availableOptions,
        ]);
    }
    
    public function storeProductType(Request $request)
    {
        try {
            $name = $request->input('mproduct_type_name');

            $type = new Mproduct_type();
            $type->mproduct_type_name = $name;
            $type->save();

            return response()->json([
                'success' => true,
                'mproduct_type_id' => $type->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not insert product type'], 500);
        }
    }
    
    public function storeBrand(Request $request)
    {
        try {
            $name = $request->input('mbrand_name');
            $brand = new Mbrand();
            $brand->mbrand_name = $name;
            $brand->save();
    
            return response()->json([
                'success' => true,
                'mbrand_id' => $brand->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not insert brand'], 500);
        }
    }
    
    public function storeTag(Request $request)
    {
        try {
            $name = $request->input('mtag_name');
            $tag = new Mtag();
            $tag->mtag_name = $name;
            $tag->save();
    
            return response()->json([
                'success' => true,
                'mtag_id' => $tag->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not insert tag'], 500);
        }
    }

    public function productStoreData(Request $request)
    {
        try {
            $pimagepath = null;
            if ($request->hasFile('pimage')) {
                $image = $request->file('pimage');
                $filename = 'mproduct_' . uniqid() . '.png';

                $img = Image::make($image->getRealPath())->resize(600, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $pimagepath = 'goapp/images/mproduct/' . $filename;
                Storage::disk('s3')->put($pimagepath, (string)$img->encode());
            }

            $mproduct = Mproduct::create([
                'mproduct_title'   => $request->ptitle,
                'mproduct_slug'    => Str::slug(strtolower($request->ptitle), '-') . "-" . uniqid(),
                'mproduct_image'   => $pimagepath,
                'mproduct_desc'    => $request->pdesc ?? "",
                'status'           => $request->pstatus ?? 'Draft',
                'saleschannel'     => $request->pchannel ?? 'Online Store',
                'product_deal_tag' => $request->product_deal_tag ?? '',
                'product_offer'    => $request->product_offer ?? '',
                'mproduct_type_id' => $request->ptype ?? null,
                'mbrand_id'        => $request->pbrand ?? null,
                'mtags'            => json_decode($request->ptags, true) ?? [],
            ]);

            if ($request->has('variants')) {
                foreach ($request->variants as $index => $variant) {
                    $vimagepath = null;
                    if ($request->hasFile("variants.$index.variantImage")) {
                        $file = $request->file("variants.$index.variantImage");
                        $filename = 'mvproduct_' . uniqid() . '.png';

                        $img = Image::make($file->getRealPath())->resize(600, 800, function ($constraint) {
                            $constraint->aspectRatio();
                        });

                        $vimagepath = 'goapp/images/mvproduct/' . $filename;
                        Storage::disk('s3')->put($vimagepath, (string)$img->encode());
                    }

                    $mvariant = Mvariant::create([
                        'mproduct_id'       => $mproduct->mproduct_id,
                        'sku'               => $variant['sku'] ?? '',
                        'mvariant_image'    => $vimagepath,
                        'price'             => $variant['price'] ?? 0,
                        'compare_price'     => $request->pcompareprice ?? 0,
                        'cost_price'        => $request->pcostprice ?? 0,
                        'taxable'           => $request->taxable ?? 0,
                        'barcode'           => $variant['barcode'] ?? '',
                        'weight'            => $request->pweight ?? 0,
                        'weightunit'        => $request->pweightunit ?? 'kg',
                        'isvalidatedetails' => 1,
                    ]);

                    Mvariant_detail::create([
                        'mvariant_id'  => $mvariant->mvariant_id,
                        'options'      => json_decode($variant['optname'],true),
                        'option_value' => json_decode($variant['optvalue'],true),
                    ]);

                    $mlocation = Mlocation::firstOrCreate(
                        ['name' => "default", 'is_default' => true],
                        ['adresss' => "default location"]
                    );

                    Mstock::create([
                        'quantity'     => $variant['stock'] ?? 0,
                        'mlocation_id' => $mlocation->mlocation_id,
                        'mvariant_id'  => $mvariant->mvariant_id,
                    ]);
                }
            } else {
                $mvariant = Mvariant::create([
                    'sku'           => $request->psku ?? '',
                    'variantImage'  => null,
                    'price'         => $request->pprice ?? 0,
                    'compare_price' => $request->pcompareprice ?? 0,
                    'cost_price'    => $request->pcostprice ?? 0,
                    'barcode'       => $request->barcode ?? '',
                    'weight'        => $request->pweight ?? 0,
                    'weightunit'    => $request->pweightunit ?? 'kg',
                    'mproduct_id'   => $mproduct->mproduct_id,
                    'isvalidatedetails' => 0,
                ]);

                Mvariant_detail::create([
                    'mvariant_id'  => $mvariant->mvariant_id,
                    'options'      => [],
                    'option_value' => [],
                ]);

                $mlocation = Mlocation::firstOrCreate(
                    ['name' => "default", 'is_default' => true],
                    ['adresss' => "default location"]
                );

                Mstock::create([
                    'quantity'     => $request->pstock ?? 0,
                    'mlocation_id' => $mlocation->mlocation_id,
                    'mvariant_id'  => $mvariant->mvariant_id,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Product saved successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function adminProductEdit($mproduct_id)
    {
        $mproductid = $mproduct_id;
        return view('admin.product.edit',compact('mproductid'));
    }

    public function productEditData(Request $request)
    {
        $mproduct = Mproduct::with('mvariants')->find($request->mproid);

        if (!$mproduct) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        foreach ($mproduct->mvariants as $mvars) {
            $mvars->options = json_decode($mvars->options, true);
            $mvars->option_value = json_decode($mvars->option_value, true);
        }

        $mptypes  = Mproduct_type::all();
        $mbrands  = Mbrand::all();
        $mtags    = Mtag::all();
        $moptions = Moption::all();

        return response()->json([
            'status'   => true,
            'mproduct' => $mproduct,
            'mptypes'  => $mptypes,
            'mbrands'  => $mbrands,
            'mtags'    => $mtags,
            'moptions' => $moptions,
        ]);
    }

    public function updateProductData(Request $request)
    {
        try {
            // 1) Locate the existing product
            $mproduct = Mproduct::findOrFail($request->mproduct_id);

            // 2) Process product image if a new file is uploaded
            $pimagepath = $mproduct->mproduct_image;
            if ($request->hasFile('pimage')) {
                // Delete the old image if it exists in S3
                if (!empty($pimagepath) && Storage::disk('s3')->exists($pimagepath)) {
                    Storage::disk('s3')->delete($pimagepath);
                }
                $image = $request->file('pimage');
                $filename = 'mproduct_' . uniqid() . '.png';
                $img = Image::make($image->getRealPath())->resize(600, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $pimagepath = 'goapp/images/mproduct/' . $filename;
                Storage::disk('s3')->put($pimagepath, (string)$img->encode());
            }

            // 3) Update main product fields
            $mproduct->update([
                'mproduct_title'   => $request->ptitle,
                'mproduct_slug'    => Str::slug(strtolower($request->ptitle), '-') . "-" . uniqid(),
                'mproduct_image'   => $pimagepath,
                'mproduct_desc'    => $request->pdesc ?? "",
                'status'           => $request->pstatus ?? 'Draft',
                'saleschannel'     => $request->pchannel ?? 'Online Store',
                'product_deal_tag' => $request->product_deal_tag ?? '',
                'product_offer'    => $request->product_offer ?? '',
                'mproduct_type_id' => $request->ptype ?? null,
                'mbrand_id'        => $request->pbrand ?? null,
                'mtags'            => json_decode($request->ptags, true) ?? [],
            ]);

            // 4) Process variants only if provided and optname is non-empty
            if ($request->has('variants') && !empty(json_decode($request->variants[0]['optname'], true))) {
                $incomingVariantIds = []; // To store variant IDs from request

                foreach ($request->variants as $index => $variantData) {
                    // If mvariant_id exists, update the variant
                    if (!empty($variantData['mvariant_id'])) {
                        $incomingId = (int)$variantData['mvariant_id'];
                        $incomingVariantIds[] = $incomingId;

                        $mvariant = Mvariant::find($incomingId);
                        if ($mvariant) {
                            $vimagepath = $mvariant->mvariant_image;
                            if ($request->hasFile("variants.$index.variantImage")) {
                                // Delete the old image if it exists in S3
                                if (!empty($vimagepath) && Storage::disk('s3')->exists($vimagepath)) {
                                    Storage::disk('s3')->delete($vimagepath);
                                }
                                $file = $request->file("variants.$index.variantImage");
                                $filename = 'mvproduct_' . uniqid() . '.png';
                                $img = Image::make($file->getRealPath())->resize(600, 800, function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                                $vimagepath = 'goapp/images/mvproduct/' . $filename;
                                Storage::disk('s3')->put($vimagepath, (string)$img->encode());
                            }
                            $mvariant->update([
                                'sku'           => $variantData['sku'] ?? '',
                                'mvariant_image'=> $vimagepath,
                                'price'         => $variantData['price'] ?? 0,
                                'compare_price' => $request->pcompareprice ?? 0,
                                'cost_price'    => $request->pcostprice ?? 0,
                                'taxable'       => $request->taxable ?? 0,
                                'barcode'       => $variantData['barcode'] ?? '',
                                'weight'        => $request->pweight ?? 0,
                                'weightunit'    => $request->pweightunit ?? 'kg',
                                'isvalidatedetails' => 1,
                            ]);

                            // Update variant details
                            $mvariantDetail = Mvariant_detail::where('mvariant_id', $mvariant->mvariant_id)->first();
                            $options = json_decode($variantData['optname'], true);
                            $optionValue = json_decode($variantData['optvalue'], true);
                            if ($mvariantDetail) {
                                $mvariantDetail->update([
                                    'options'      => $options,
                                    'option_value' => $optionValue,
                                ]);
                            } else {
                                Mvariant_detail::create([
                                    'mvariant_id'  => $mvariant->mvariant_id,
                                    'options'      => $options,
                                    'option_value' => $optionValue,
                                ]);
                            }

                            // Update stock record
                            $mlocation = Mlocation::firstOrCreate(
                                ['name' => "default", 'is_default' => true],
                                ['adresss' => "default location"]
                            );
                            $mstock = Mstock::where('mvariant_id', $mvariant->mvariant_id)->first();
                            if ($mstock) {
                                $mstock->update([
                                    'quantity'     => $variantData['stock'] ?? 0,
                                    'mlocation_id' => $mlocation->mlocation_id,
                                ]);
                            } else {
                                Mstock::create([
                                    'quantity'     => $variantData['stock'] ?? 0,
                                    'mlocation_id' => $mlocation->mlocation_id,
                                    'mvariant_id'  => $mvariant->mvariant_id,
                                ]);
                            }
                        }
                    }
                    // Otherwise, create a new variant
                    else {
                        $vimagepath = null;
                        if ($request->hasFile("variants.$index.variantImage")) {
                            $file = $request->file("variants.$index.variantImage");
                            $filename = 'mvproduct_' . uniqid() . '.png';
                            $img = Image::make($file->getRealPath())->resize(600, 800, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                            $vimagepath = 'goapp/images/mvproduct/' . $filename;
                            Storage::disk('s3')->put($vimagepath, (string)$img->encode());
                        }
                        $mvariant = Mvariant::create([
                            'mproduct_id'    => $mproduct->mproduct_id,
                            'sku'            => $variantData['sku'] ?? '',
                            'mvariant_image' => $vimagepath,
                            'price'          => $variantData['price'] ?? 0,
                            'compare_price'  => $request->pcompareprice ?? 0,
                            'cost_price'     => $request->pcostprice ?? 0,
                            'taxable'        => $request->taxable ?? 0,
                            'barcode'        => $variantData['barcode'] ?? '',
                            'weight'         => $request->pweight ?? 0,
                            'weightunit'     => $request->pweightunit ?? 'kg',
                            'isvalidatedetails' => 1,
                        ]);

                        // Add newly created variant id to the incoming array
                        $incomingVariantIds[] = $mvariant->mvariant_id;

                        Mvariant_detail::create([
                            'mvariant_id'  => $mvariant->mvariant_id,
                            'options'      => json_decode($variantData['optname'], true),
                            'option_value' => json_decode($variantData['optvalue'], true),
                        ]);
                        $mlocation = Mlocation::firstOrCreate(
                            ['name' => "default", 'is_default' => true],
                            ['adresss' => "default location"]
                        );
                        Mstock::create([
                            'quantity'     => $variantData['stock'] ?? 0,
                            'mlocation_id' => $mlocation->mlocation_id,
                            'mvariant_id'  => $mvariant->mvariant_id,
                        ]);
                    }
                }

                // 5) Delete any existing variant records that are NOT in the incomingVariantIds
                $allExistingVariants = Mvariant::where('mproduct_id', $mproduct->mproduct_id)->get();
                foreach ($allExistingVariants as $oldVariant) {
                    if (!in_array($oldVariant->mvariant_id, $incomingVariantIds)) {
                        // Delete the variant image from S3 if it exists
                        if (!empty($oldVariant->mvariant_image) && Storage::disk('s3')->exists($oldVariant->mvariant_image)) {
                            Storage::disk('s3')->delete($oldVariant->mvariant_image);
                        }
                        // Delete associated variant detail and stock before deleting the variant record
                        Mvariant_detail::where('mvariant_id', $oldVariant->mvariant_id)->delete();
                        Mstock::where('mvariant_id', $oldVariant->mvariant_id)->delete();
                        // Delete the variant record from the database
                        $oldVariant->delete();
                    }
                }
            }
            // 6) Fallback: If no valid variants are provided, update the single main variant if it exists
            else {
                $mvariant = Mvariant::where('mproduct_id', $mproduct->mproduct_id)->first();
                if ($mvariant) {
                    $mvariant->update([
                        'sku'           => $request->psku ?? '',
                        'mvariant_image'=> null,
                        'price'         => $request->pprice ?? 0,
                        'compare_price' => $request->pcompareprice ?? 0,
                        'cost_price'    => $request->pcostprice ?? 0,
                        'barcode'       => $request->pbarcode ?? '',
                        'weight'        => $request->pweight ?? 0,
                        'weightunit'    => $request->pweightunit ?? 'kg',
                        'isvalidatedetails' => 0,
                    ]);
                    $mvariantDetail = Mvariant_detail::where('mvariant_id', $mvariant->mvariant_id)->first();
                    if ($mvariantDetail) {
                        $mvariantDetail->update([
                            'options'      => [],
                            'option_value' => [],
                        ]);
                    } else {
                        Mvariant_detail::create([
                            'mvariant_id'  => $mvariant->mvariant_id,
                            'options'      => [],
                            'option_value' => [],
                        ]);
                    }
                    $mlocation = Mlocation::firstOrCreate(
                        ['name' => "default", 'is_default' => true],
                        ['adresss' => "default location"]
                    );
                    $mstock = Mstock::where('mvariant_id', $mvariant->mvariant_id)->first();
                    if ($mstock) {
                        $mstock->update([
                            'quantity'     => $request->pstock ?? 0,
                            'mlocation_id' => $mlocation->mlocation_id,
                        ]);
                    } else {
                        Mstock::create([
                            'quantity'     => $request->pstock ?? 0,
                            'mlocation_id' => $mlocation->mlocation_id,
                            'mvariant_id'  => $mvariant->mvariant_id,
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
