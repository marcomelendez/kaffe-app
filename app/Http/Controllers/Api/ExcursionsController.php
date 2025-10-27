<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExcursionResources;
use App\Http\Resources\PackageResources;
use App\Models\Excursion;
use Illuminate\Http\Request;

class ExcursionsController extends Controller
{
    public function index()
    {
        $packages = Excursion::active()->paginate(20);
        return response()->apiSuccess(ExcursionResources::collection($packages), 'Excursions Listed');
    }

    public function top_five()
    {
        $packages = Excursion::active()
            ->take(5)
            ->get();

        // Logic to retrieve top 5 packages
        return response()->apiSuccess(ExcursionResources::collection($packages), 'Top 5 Excursions Listed');
    }
}
