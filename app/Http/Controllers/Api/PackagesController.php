<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResources;
use App\Models\Package;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    public function index()
    {
        $packages = Package::active()->paginate(20);
        return response()->apiSuccess(PackageResources::collection($packages), 'Packages Listed');
    }

    public function top_five()
    {
        $packages = Package::active()
            ->take(5)
            ->get();

        // Logic to retrieve top 5 packages
        return response()->apiSuccess(PackageResources::collection($packages), 'Top 5 Packages Listed');
    }
}
