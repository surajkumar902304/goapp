<?php

namespace App\Http\Controllers;

use App\Models\Mvariant;
use App\Models\User;
use App\Models\UserTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class UserTagController extends Controller
{
    public function userTagVlist()
    {
        $userTags = UserTag::orderBy('user_tag_id','desc')->get();
        return response()->json([
            'status' => true,
            'userTags' => $userTags,
        ],200);
    }

    public function addUserTag(Request $request)
    {
        $request->validate([
            'user_tag_name'  => 'required|string|max:50',
        ]);

        $tag                 = new UserTag();
        $tag->user_tag_name  = $request->user_tag_name;

        $column = $request->user_tag_name;

        if (Schema::hasColumn('mvariants', $column)) {
            return response()->json(['success' => false, 'message' => "Column `$column` already exists in mvariants."]);
        }

        Schema::table('mvariants', function (Blueprint $table) use ($column) {
            $table->float($column)->nullable();
        });

        $tag->save();

        return response()->json(['status' => true]);
    }

    // public function editUserTag(Request $request)
    // {
    //     $request->validate([
    //         'user_tag_id'    => 'required|exists:user_tags,user_tag_id',
    //         'user_tag_name'  => 'required|string|max:255',
    //     ]);

    //     $tag = UserTag::find($request->user_tag_id);
    //     $tag->user_tag_name  = $request->user_tag_name;
    //     $tag->save();

    //     return response()->json(['status' => true]);
    // }

    public function deleteUserTag(Request $request)
    {
        $request->validate([
            'user_tag_id' => 'required|exists:user_tags,user_tag_id',
        ]);

        try {
            $tag = UserTag::findOrFail($request->user_tag_id);
            $column = $tag->user_tag_name;

            Schema::table('mvariants', function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });

            $tag->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function variantForTagPrice(Request $request)
    {
        $tag = UserTag::find($request->UserTagPrice);

        if (!$tag) {
            return response()->json(['error' => 'Invalid Tag ID'], 404);
        }

        $field = $tag->user_tag_name; 

        $variants = Mvariant::select('mvariant_id', 'mvariant_image', 'price', 'mproduct_id', $field)
            ->with([
                'product' => function ($q) {
                    $q->select('mproduct_id', 'mproduct_title', 'mproduct_image', 'status', 'saleschannel');
                },
                'details:mvariant_detail_id,mvariant_id,options,option_value',
            ])
            ->get()
            ->filter(fn($v) => $v->product)
            ->map(function ($variant) use ($field) {
                $data = $variant->toArray();
                $data[$field] = $variant->$field ?? null;
                return $data;
            })
            ->values();

        return response()->json([
            'variants' => $variants,
        ]);
    }

    public function updateTagPrice(Request $request)
    {
        $request->validate([
            'mvariant_id' => 'required|integer',
            'field' => 'required|string',
            'value' => 'nullable|numeric',
        ]);

        $variant = Mvariant::findOrFail($request->mvariant_id);

        $field = $request->field;

        if (!Schema::hasColumn('mvariants', $field)) {
            return response()->json(['error' => 'Invalid field'], 422);
        }

        $variant->$field = $request->value ?? 0;
        $variant->save();

        return response()->json(['success' => true, 'message' => 'Value updated']);
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

        return response()->json(['success' => true,'message' => 'Tag assigned successfully']);
    }

}
