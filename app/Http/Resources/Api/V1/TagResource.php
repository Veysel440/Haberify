<?php

namespace App\Http\Resources\Api\V1;


use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray($request): array
    { return ['id'=>$this->id,'name'=>$this->name,'slug'=>$this->slug,'is_active'=>$this->is_active]; }
}
