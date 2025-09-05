<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMethod;
use App\Models\ServiceSolution;
use App\Models\UserCompanyAddress;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CompanyAddressController extends Controller
{
    public function index()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found from.',
                ], 404);
            }

            $company_address = UserCompanyAddress::where('user_id', $user->id)->get();

            $delivery_methods = DeliveryMethod::where('is_active', '=','1')->get();

            return response()->json([
                'status'    => true,
                'message'   => 'Fetched all Company Addresses & Delivery Methods successfully',
                'company_addresses' => $company_address,
                'delivery_methods' => $delivery_methods,
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(['status' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => false, 'message' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'message' => 'Token not found'], 401);
        }
    }

    public function upsertCompanyAddress(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 404);
            }

            $request->validate([
                'user_company_address_id' => 'nullable|exists:user_company_addresses,user_company_address_id',
                'user_company_name'       => 'required|string|max:255',
                'company_address1'        => 'required|string|max:255',
                'company_address2'        => 'nullable|string|max:255',
                'company_city'            => 'required|string|max:255',
                'company_country'         => 'required|string|max:255',
                'company_postcode'        => 'required|string|max:255',
            ]);

            $data = $request->only([
                'user_company_name',
                'company_address1',
                'company_address2',
                'company_city',
                'company_country',
                'company_postcode'
            ]);

            if ($request->user_company_address_id) {
                $address = UserCompanyAddress::where('user_id', $user->id)
                            ->where('user_company_address_id', $request->user_company_address_id)
                            ->first();

                if (!$address) {
                    return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
                }

                $address->update($data);
                $message = 'Address updated successfully.';
            } else {
                $data['user_id'] = $user->id;
                UserCompanyAddress::create($data);
                $message = 'Address created successfully.';
            }

            return response()->json(['status' => true, 'message' => $message], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteCompanyAddress(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 404);
            }

            $request->validate([
                'user_company_address_id' => 'required|exists:user_company_addresses,user_company_address_id',
            ]);

            $deleted = UserCompanyAddress::where('user_id', $user->id)
                        ->where('user_company_address_id', $request->user_company_address_id)
                        ->delete();

            if ($deleted) {
                return response()->json(['status' => true, 'message' => 'Address deleted successfully.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Address not found or not deleted.']);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting address.', 'error' => $e->getMessage()], 500);
        }
    }


    //Delivery Methods
    public function deliveryMethod(){

        $delivery_methods = DeliveryMethod::where('is_active', 1)->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Fetched all Delivery Methods successfully',
            'delivery_methods' => $delivery_methods,
        ], 200);
    }

    // Service & Solutions
    public function serviceAndSolution()
    {
        $service_solutions = ServiceSolution::get();

        return response()->json([
            'status'    => true,
            'message'   => 'Fetched all Delivery Methods successfully',
            'service_solutions' => $service_solutions,
        ], 200);
    }
   
}
