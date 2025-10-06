<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyDetailsResource;
use App\Models\Property;
use Illuminate\Http\Request;

class HotelsController extends Controller
{
    public function index()
    {
        return response()->apiSuccess(Property::active()->paginate(20)->toResourceCollection(), 'Hoteles Listados');
    }

    public function show($slug)
    {
        $property = Property::where('slug', $slug)->first();

        if (!$property) {
            return response()->apiError('property not found', 404);
        }

        return response()->apiSuccess(new PropertyDetailsResource($property), 'Hoteles Listados');
    }

    public function top_three()
    {
        $topHotels3 = explode(',', env('TOP_HOTELS_3', '1,43,52'));
        $properties = Property::whereIn('id', $topHotels3)->get();
        return response()->apiSuccess($properties->toResourceCollection(), 'Top 3 Hoteles Listados');
    }
}
