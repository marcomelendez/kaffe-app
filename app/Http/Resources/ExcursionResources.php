<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ExcursionResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->translate('es')->name ?? '',
            'description'=>$this->translate('es')->description ?? '',
            'short_description'=>$this->translate('es')->short_description ?? '',
            'slug'=>$this->slug,
            'type'=>$this->type,
            'image_url'=> $this->image_url ? Storage::disk('s3')->url($this->image_url) : null,
            'price_default'=>$this->price_default,
            'available_from'=> Carbon::parse($this->available_from)->format('d/m/Y'),
            'available_to'=> Carbon::parse($this->available_to)->format('d/m/Y'),
            'location_id'=>$this->location_id,
            'is_active'=>$this->is_active,
        ];
    }
}
