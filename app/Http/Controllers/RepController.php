<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OrderCommission;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Http\Request;

class RepController extends Controller
{
    public function repVlist()
    {
        $reps = Customer::select(
                    'rep_id', 'name', 'email', 'mobile',
                    'rep_code', 'commission_percent'
                )
                ->latest('rep_id')
                ->get();

        foreach ($reps as $rep) {
            $rep->total_commission = OrderCommission::where('rep_id', $rep->rep_id)->sum('commission_amount');
        }

        return response()->json(['reps' => $reps]);
    }

    public function addRep(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:customers,email',
            'mobile'             => 'required|string|max:15',
            'rep_code'           => 'required|string|max:20|unique:customers,rep_code',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'password'           => 'required|string|min:6',
        ]);

        $data['rep_code'] = strtoupper($data['rep_code']);
        $data['password'] = bcrypt($data['password']);   

        $rep = Customer::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Rep created successfully',
            'rep'     => $rep,
        ], 201);
    }

    public function editRep(Request $request, $id)
    {
        $rep = Customer::findOrFail($id);

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => [
                'required','email',
                Rule::unique('customers', 'email')->ignore($rep->rep_id, 'rep_id')
            ],
            'mobile'             => 'required|string|max:15',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'rep_code'           => [
                'required','string','max:20',
                Rule::unique('customers','rep_code')->ignore($rep->rep_id,'rep_id')
            ],
            'password'           => 'nullable|string|min:6',
        ]);

        $data['rep_code'] = strtoupper($data['rep_code']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);      
        }

        $rep->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Rep updated successfully',
            'rep'     => $rep
        ]);
    }

    public function deleteRep(Request $request)
    {
        $id = $request->validate(['rep_id' => 'required|exists:customers,rep_id'])['rep_id'];

        Customer::where('rep_id', $id)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Rep deleted permanently'
        ]);
    }

    public function checkRepCode($code)
    {
        $rep = Customer::where('rep_code', strtoupper($code))->first();

        if (!$rep) {
            return response()->json(['status' => false, 'message' => 'Invalid rep code']);
        }

        return response()->json(['status' => true, 'data' => $rep]);
    }

    // Admin Assign Manual rep code
    public function assignRep(Request $request) 
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rep_id' => 'required|exists:customers,rep_id',
        ]);

        $user = User::find($request->user_id);
        $user->rep_id = $request->rep_id;
        $user->save();

        return response()->json(['message' => 'Rep assigned successfully']);
    }

}
