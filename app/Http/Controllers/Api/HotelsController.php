<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyDetailsResource;
use App\Models\Property;
use Illuminate\Http\Request;

class HotelsController extends Controller
{

    CONST TOP_HOTELS_3 = [1,5,6];


    public function index()
    {
        return response()->apiSuccess(Property::paginate()->toResourceCollection(), 'Hoteles Listados');
    }

    public function show($id)
    {
        $property = Property::where('slug', $id)->first();

        if (!$property) {
            return response()->apiError('property not found', 404);
        }

        return response()->apiSuccess(new PropertyDetailsResource($property), 'Hoteles Listados');
    }

    public function top_three()
    {
        $properties = Property::whereIn('id', self::TOP_HOTELS_3)->get();
        return response()->apiSuccess($properties->toResourceCollection(), 'Top 3 Hoteles Listados');
    }
}
