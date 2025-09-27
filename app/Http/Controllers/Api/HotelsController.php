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
        return response()->apiSuccess(Property::paginate()->toResourceCollection(), 'Hoteles Listados');
    }

    public function show($id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->apiError('property not found', 404);
        }

        return response()->apiSuccess(new PropertyDetailsResource($property), 'Hoteles Listados');
    }
}
