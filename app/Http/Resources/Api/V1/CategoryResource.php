<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,'name'=>$this->name,'slug'=>$this->slug,
            'description'=>$this->description,'parent_id'=>$this->parent_id,
            'is_active'=>$this->is_active,
        ];
    }
}
