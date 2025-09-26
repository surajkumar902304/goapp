<?php

namespace App\Http\Controllers;

use App\Models\Mvariant;
use App\Models\User;
use App\Models\UserTag;
use App\Models\UserTagPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Validation\Rule;

class UserTagController extends Controller
{
    public function userTagVlist()
    {
        $userTags = UserTag::orderBy('user_tag_id', 'desc')->get();
        return response()->json([
            'status' => true,
            'userTags' => $userTags,
        ], 200);
    }

    public function addUserTag(Request $request)
    {
        $request->validate([
            'user_tag_name' => ['required', 'string', 'max:50', 'unique:user_tags,user_tag_name'],
            'type' => ['required', Rule::in(['custom', 'percentage'])],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $tag = new UserTag();
        $tag->user_tag_name = $request->user_tag_name;
        $tag->type = $request->type;
        $tag->discount = $request->discount;

        $tag->save();

        return response()->json(['status' => true]);
    }

    public function editUserTag(Request $request)
    {
        $request->validate([
            'user_tag_id' => 'required|exists:user_tags,user_tag_id',
            'user_tag_name' => ['required', 'string', 'max:50', 'unique:user_tags,user_tag_name'],
        ]);

        $tag = UserTag::find($request->user_tag_id);
        $tag->user_tag_name = $request->user_tag_name;
        $tag->save();

        return response()->json(['status' => true]);
    }

    public function deleteUserTag(Request $request)
    {
        $request->validate([
            'user_tag_id' => 'required|exists:user_tags,user_tag_id',
        ]);

        try {
            $tag = UserTag::findOrFail($request->user_tag_id);

            $tag->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function userTagToggleStatus(Request $request, $id)
    {
        $tag = UserTag::findOrFail($id);
        $tag->is_active = $request->is_active;
        $tag->save();

        return response()->json(['status' => true, 'message' => 'Status updated.']);
    }



    public function variantForTagPrice(Request $request)
    {
        $tag = UserTag::where('user_tag_id', $request->UserTagPrice)
            ->where('type', 'custom')
            ->first();

        if (!$tag) {
            return response()->json(['error' => 'Invalid or non-custom tag'], 404);
        }

        $variants = Mvariant::select('mvariant_id', 'mvariant_image', 'price', 'mproduct_id')
            ->with([
                'product' => function ($q) {
                    $q->select('mproduct_id', 'mproduct_title', 'mproduct_image', 'status', 'saleschannel');
                },
                'details:mvariant_detail_id,mvariant_id,options,option_value',
            ])
            ->get()
            ->filter(fn($v) => $v->product)
            ->map(function ($variant) use ($tag) {
                $existingPrice = UserTagPrice::where('user_tag_id', $tag->user_tag_id)
                    ->where('mvariant_id', $variant->mvariant_id)
                    ->value('tag_price');

                return [
                    'mvariant_id' => $variant->mvariant_id,
                    'variant_image' => $variant->mvariant_image,
                    'price' => $variant->price,
                    'tag_price' => $existingPrice ?? null,
                    'product' => $variant->product,
                    'details' => $variant->details,
                ];
            })
            ->values();

        return response()->json([
            'variants' => $variants,
            'tagName' => $tag->user_tag_name,
        ]);
    }

    public function updateTagPrice(Request $request)
    {
        $request->validate([
            'user_tag_id' => 'required|exists:user_tags,user_tag_id',
            'mvariant_id' => 'required|exists:mvariants,mvariant_id',
            'tag_price' => 'required|numeric|min:0',
        ]);

        UserTagPrice::updateOrCreate(
            [
                'user_tag_id' => $request->user_tag_id,
                'mvariant_id' => $request->mvariant_id,
            ],
            [
                'tag_price' => $request->tag_price,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Tag price saved successfully',
        ]);
    }

    // Admin Assign Manual rep code
    public function assignTag(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_tag_id' => 'required|exists:user_tags,user_tag_id',
        ]);

        $user = User::find($request->user_id);
        $user->user_tag_id = $request->user_tag_id;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Tag assigned successfully']);
    }

}
