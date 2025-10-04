<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        try {
            return [
                'id' => $this->id,
                'code' => $this->code,
                'name' => $this->name,
                'description' => $this->description,
                'status' => $this->status

            ];
        } catch (\Exception $e) {
            dd($this->resource);
        }
    }
}
