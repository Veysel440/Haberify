<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray($req): array {
        return [
            'id'=>$this->id,'title'=>$this->title,'slug'=>$this->slug,
            'body'=>$this->body,'meta'=>$this->meta,'status'=>$this->status,
            'updated_at'=>$this->updated_at,
        ];
    }
}
