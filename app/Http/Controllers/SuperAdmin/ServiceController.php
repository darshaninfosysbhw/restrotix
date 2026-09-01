<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuperAdmin\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{

    public function index()
    {
        $services = Service::latest()->get();
        $servicesMapped = collect(ServiceResource::collection($services)->resolve());

        $actualRevenue = DB::table('tenant_service')
            ->join('services', 'tenant_service.service_id', '=', 'services.id')
            ->where('tenant_service.status', 'active')
            ->where(function ($query) {
                $query->whereNull('tenant_service.expires_at')
                    ->orWhere('tenant_service.expires_at', '>', now());
            })
            ->sum('services.price');

        $serviceStats = [
            'total' => $servicesMapped->count(),
            'active' => $servicesMapped->where('status', 'Active')->count(),
            'inactive' => $servicesMapped->where('status', 'Inactive')->count(),
            'revenue' => $actualRevenue,
        ];

        return view('superadmin.services.index', [
            'services' => $servicesMapped,
            'serviceStats' => $serviceStats,
        ]);
    }

    /**
     * नई सर्विस को डेटाबेस में सेव करो
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:services,slug',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [
                [
                    'type' => 'error',
                    'message' => $validator->errors()->first(),
                    'duration' => 5000,
                ],
            ]);
        }

        try {
            Service::create($validator->validated());

            return redirect()->back()->with('toast', [
                ['type' => 'success', 'message' => 'Service added successfully!', 'duration' => 5000]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }

    /**
     * पुरानी सर्विस को अपडेट करो
     */
    public function update(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:services,slug,' . $service->id,
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [
                [
                    'type' => 'error',
                    'message' => $validator->errors()->first(),
                    'duration' => 5000,
                ],
            ]);
        }

        try {
            $service->update($validator->validated());

            return redirect()->back()->with('toast', [
                ['type' => 'success', 'message' => 'Service updated successfully!', 'duration' => 5000]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }

    /**
     * सर्विस को डिलीट करो
     */
    public function destroy(Service $service)
    {
        try {
            $service->delete();

            return redirect()->back()->with('toast', [
                ['type' => 'success', 'message' => 'Service deleted successfully!', 'duration' => 5000]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('toast', [
                ['type' => 'error', 'message' => 'Unable to delete service: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }
}
