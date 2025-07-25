<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsHistoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'news_id'     => $this->news_id,
            'edited_by'   => $this->edited_by,
            'editor_name' => optional($this->editor)->name,
            'title'       => $this->title,
            'excerpt'     => $this->excerpt,
            'slug'        => $this->slug,
            'status'      => $this->status,
            'image'       => $this->image,
            'scheduled_at'=> $this->scheduled_at,
            'updated_at'  => $this->updated_at->toDateTimeString(),
        ];
    }
}
