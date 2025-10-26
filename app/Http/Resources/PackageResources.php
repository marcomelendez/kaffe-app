<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PackageResources extends JsonResource
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
            'price'=>$this->price,
            'duration_days'=>$this->duration_days,
            'duration_nights'=>$this->duration_nights,
            'slug'=>$this->slug,
            'image_url'=> $this->image_path ? Storage::disk('s3')->url($this->image_path) : null,
            'available_from'=> Carbon::parse($this->available_from)->format('d/m/Y'),
        ];
    }
}
