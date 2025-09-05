<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    public function index()
    {
        $bankdetails = BankDetail::get();
        return response()->json([
            'status' => true,
            'bankdetails' => $bankdetails,
        ],200);
    }

    public function addBankDetail(Request $request)
    {
        $request->validate([
            'company_name'    => ['required', 'string', 'max:255'],
            'bank_name'       => ['required', 'string', 'max:255'],
            'account_number'  => ['required', 'string', 'max:255'],
            'sort_code'       => ['required', 'string', 'max:255'],
            'note'            => ['required', 'string', 'max:255'],
        ]);

        $bank_detail = new BankDetail();
        $bank_detail->company_name   = $request->company_name;
        $bank_detail->bank_name      = $request->bank_name;
        $bank_detail->account_number = $request->account_number;
        $bank_detail->sort_code      = $request->sort_code;
        $bank_detail->note           = $request->note;
        $bank_detail->save();

        return response()->json(['status' => true]);
    }

    public function bankDetailToggleStatus(Request $request, $id)
    {
        $bank_detail = BankDetail::findOrFail($id);
        $bank_detail->is_active = $request->is_active;
        $bank_detail->save();

        return response()->json(['status' => true, 'message' => 'Status updated.']);
    }

    public function editBankDetail(Request $request)
    {
        $request->validate([
            'bank_detail_id' => 'required|exists:bank_details,bank_detail_id',
            'company_name'   => ['required', 'string', 'max:255'],
            'bank_name'      => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'sort_code'      => ['required', 'string', 'max:255'],
            'note'           => ['required', 'string', 'max:255'],
        ]);

        $bank_detail = BankDetail::find($request->bank_detail_id);
        $bank_detail->company_name   = $request->company_name;
        $bank_detail->bank_name      = $request->bank_name;
        $bank_detail->account_number = $request->account_number;
        $bank_detail->sort_code      = $request->sort_code;
        $bank_detail->note           = $request->note;
        $bank_detail->save();

        return response()->json(['status' => true]);
    }

    public function deleteBankDetail(Request $request)
    {
        $request->validate([
            'bank_detail_id' => 'required|exists:bank_details,bank_detail_id',
        ]);

        try {
            $bank_detail = BankDetail::findOrFail($request->bank_detail_id);

            $bank_detail->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
