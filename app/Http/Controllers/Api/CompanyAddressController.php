<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserCompanyAddress;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CompanyAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

            return response()->json([
                'status'    => true,
                'message'   => 'Fetched all Company Addresses successfully',
                'company_addresses' => $company_address,
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(['status' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => false, 'message' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'message' => 'Token not found'], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 404);
            }

            $request->validate([
                'addresses'                         => 'required|array|min:1',
                'addresses.*.user_company_name'     => 'required|string|max:255',
                'addresses.*.company_address1'      => 'required|string|max:255',
                'addresses.*.company_address2'      => 'nullable|string|max:255',
                'addresses.*.company_city'          => 'required|string|max:255',
                'addresses.*.company_country'       => 'required|string|max:255',
                'addresses.*.company_postcode'      => 'required|string|max:255',
            ]);

            $incomingAddresses = collect($request->addresses);
            $existingAddresses = UserCompanyAddress::where('user_id', $user->id)->get();

            $incomingIds = $incomingAddresses->pluck('user_company_address_id')->filter()->all();
            $existingIds = $existingAddresses->pluck('user_company_address_id')->all();

            // Delete address
            $toDelete = array_diff($existingIds, $incomingIds);
            UserCompanyAddress::where('user_id', $user->id)->whereIn('user_company_address_id', $toDelete)
                                ->delete();

            foreach ($incomingAddresses as $addr) {
                if (!empty($addr['user_company_address_id'])) {
                    // Update address
                    $existing = $existingAddresses->firstWhere('user_company_address_id', $addr['user_company_address_id']);
                    if ($existing) {
                        $existing->update([
                            'user_company_name'     => $addr['user_company_name'],
                            'company_address1'      => $addr['company_address1'],
                            'company_address2'      => $addr['company_address2'],
                            'company_city'          => $addr['company_city'],
                            'company_country'       => $addr['company_country'],
                            'company_postcode'      => $addr['company_postcode'],
                        ]);
                    }
                } else {
                    // add address
                    UserCompanyAddress::create([
                        'user_id'               => $user->id,
                        'user_company_name'     => $addr['user_company_name'],
                        'company_address1'      => $addr['company_address1'],
                        'company_address2'      => $addr['company_address2'],
                        'company_city'          => $addr['company_city'],
                        'company_country'       => $addr['company_country'],
                        'company_postcode'      => $addr['company_postcode'],
                    ]);
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Company addresses updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error processing addresses.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
   
}
