<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyDetailsResource extends JsonResource
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
            'images' => $this->getMedia('hotels')->map(fn($media) => [
                'url' => $media->getUrl(),
               //s 'thumb' => $media->getUrl('thumb'),
                'name' => $media->name,
            ]),
        ];
    }
}
