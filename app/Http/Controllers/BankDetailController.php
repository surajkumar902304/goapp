<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use App\Models\IntegrationSetting;
use App\Models\StripeIntegration;
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


    // Stripe
    public function stripeVlist()
    {
        $stripe = StripeIntegration::get();
        return response()->json([
            'status' => true,
            'stripe' => $stripe,
        ],200);
    }

    public function stripeToggleStatus(Request $request, $id)
    {
        $integration = StripeIntegration::findOrFail($id);
        $integration->is_active = $request->is_active;
        $integration->save();

        return response()->json(['status' => true, 'message' => 'Status updated.']);
    }

    public function editStripe(Request $request)
    {
        $request->validate([
            'stripe_integration_id' => 'required|exists:stripe_integrations,stripe_integration_id',
            'publishable_key'      => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
            'webhook_secret' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
            'test_mode' => ['required', 'boolean'],
        ]);

        $integration = StripeIntegration::find($request->stripe_integration_id);
        $integration->publishable_key      = $request->publishable_key;
        $integration->secret_key = $request->secret_key;
        $integration->webhook_secret = $request->webhook_secret;
        $integration->note = $request->note;
        $integration->test_mode = $request->test_mode;
        $integration->save();

        return response()->json(['status' => true]);
    }


    // Sendcloud Integration
    public function sendcloudVlist()
    {
        $integrations = IntegrationSetting::get();
        return response()->json([
            'status' => true,
            'integrations' => $integrations,
        ],200);
    }

    public function addSendcloud(Request $request)
    {
        $request->validate([
            'provider'    => ['required', 'string', 'max:255'],
            'public_key'       => ['nullable', 'string', 'max:255'],
            'secret_key'  => ['nullable', 'string', 'max:255'],
        ]);

        $integration = new IntegrationSetting();
        $integration->provider   = $request->provider;
        $integration->public_key      = $request->public_key;
        $integration->secret_key = $request->secret_key;
        $integration->save();

        return response()->json(['status' => true]);
    }

    public function sendcloudToggleStatus(Request $request, $id)
    {
        $integration = IntegrationSetting::findOrFail($id);
        $integration->is_active = $request->is_active;
        $integration->save();

        return response()->json(['status' => true, 'message' => 'Status updated.']);
    }

    public function editSendcloud(Request $request)
    {
        $request->validate([
            'integration_setting_id' => 'required|exists:integration_settings,integration_setting_id',
            'provider'   => ['required', 'string', 'max:255'],
            'public_key'      => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
        ]);

        $integration = IntegrationSetting::find($request->integration_setting_id);
        $integration->provider   = $request->provider;
        $integration->public_key      = $request->public_key;
        $integration->secret_key = $request->secret_key;
        $integration->save();

        return response()->json(['status' => true]);
    }

}
