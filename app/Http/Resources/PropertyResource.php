<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'name'=>$this->name,
            'category'=> $this->category->translate('es')->name,
            'description'=> $this->translate('es')->description ?? '',
            'slug'=>$this->slug,
            'latlng'=>$this->latlng,
            'direction'=>$this->translate('es')->directions ?? '',
            'plans'=>PlanResource::collection($this->plans),
            'main_photo_lg'=>$this->main_photo_lg,
            'main_photo_md'=>$this->main_photo_md,
            'main_photo_sm'=>$this->main_photo_sm,
            'rating'=>$this->rating,
            'stars'=>$this->stars,
        ];
    }
}
