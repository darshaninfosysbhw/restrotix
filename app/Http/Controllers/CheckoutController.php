<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;
use App\Models\SubscriptionHistory;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $planSlug = $request->query('plan', 'base');
        $planDetails = Plan::where('slug', $planSlug)->first();

        if (!$planDetails) {
            return redirect('/pricing')->with('error', 'Plan not found.');
        }

        // यहाँ हम सिर्फ सेशन से वैल्यू 'निकाल' रहे हैं
        // अगर सेशन में कुछ नहीं है, तो डिफ़ॉल्ट 1 (मान लीजिए INR) मान लेंगे
        $currencyId = session('currency_id', 1);
        $priceData = $planDetails->prices()->where('currency_id', $currencyId)->first();
        $planDetails->monthly_price = $priceData ? $priceData->monthly_price : $planDetails->base_price;


        return view('checkout', compact('planDetails'));
    }

    public function store(Request $request)
    {
        try {
            // 1. Validation
            $request->validate([
                'full_name'       => 'required|string|max:255',
                'email'           => 'required|email|unique:users,email',
                'password'        => 'required|min:8',
                'restaurant_name' => 'required|string|max:255',
                'plan_id'         => 'required|exists:plans,id',
                'phone'          => 'required|string|max:15|unique:users,phone_number',
                'city'            => 'required|string|max:100',
                'country'         => 'required|string',
                'currency'        => 'required|string|max:10',
                'timezone'        => 'required|string',
                'pincode'         => 'nullable|string|max:10', // Field optional ho sakta hai
                'currency_id' => 'required|exists:currencies,id',
            ]);

            return DB::transaction(function () use ($request) {
                // Plan details for trial days
                $plan = Plan::findOrFail($request->plan_id);
                $planPrice = $plan->prices()->where('currency_id', $request->currency_id)->first();
                $finalAmount = $planPrice ? $planPrice->monthly_price : $plan->base_price;
                // 2. Create Tenant
                $tenant = Tenant::create([
                    'company_name'  => $request->restaurant_name,
                    'owner_name'    => $request->full_name,
                    'plan_id'       => $plan->id,
                    'subscription_ends_at' => now()->addDays($plan->trial_days),
                ]);



                // 3. Create Main Branch
                $branch = Branch::create([
                    'tenant_id'      => $tenant->id,
                    'branch_name'    => $request->restaurant_name . ' - Main',
                    'contact_number' => $request->phone,
                    'branch_email'   => $request->email,
                    'country_code'   => substr($request->country, 0, 3), // e.g., 'Nep', 'Ind'
                    'currency'       => $request->currency,
                    'timezone'       => $request->timezone,
                    'state'          => $request->state ?? '',
                    'city'           => $request->city,
                    'pincode'        => $request->pincode,
                    'full_address'   => $request->city . ', ' . ($request->state ?? '') . ' (' . $request->country . ')',
                ]);


                // 4. Create User (Admin)
                $user = User::create([
                    'name'      => $request->full_name,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch->id,
                    'phone_number'     => $request->phone,
                    'role'      => 'admin',
                ]);


                // 5. Subscription History में पहली एंट्री (Trial) डालना
                SubscriptionHistory::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $request->plan_id,
                    'currency_id' => $request->currency_id,
                    'billing_cycle' => $request->billing_cycle ?? 'monthly',
                    'amount' => 0.00,
                    'status' => 'active',
                    'started_at' => now(),
                    'ended_at' => now()->addDays($plan->trial_days ?? 14), // प्लान टेबल से ट्रायल के दिन उठाएं
                ]);



                // 6. Auto Login
                Auth::login($user);

                return response()->json([
                    'success'  => true,
                    'message'  => 'Welcome ' . $request->full_name . '! Your restaurant is ready.',
                    'redirect' => route('admin.dashboard')
                ]);
            });
        } catch (ValidationException $e) {
            $errorMessages = Arr::flatten($e->errors());
            return response()->json([
                'success' => false,
                'message' => $errorMessages[0]
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}
