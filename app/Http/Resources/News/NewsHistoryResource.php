<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsHistoryResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'news_id'      => $this->news_id,
            'edited_by'    => $this->edited_by,
            'editor_name'  => $this->editor?->name,
            'title'        => $this->title,
            'excerpt'      => $this->excerpt,
            'slug'         => $this->slug,
            'status'       => $this->status,
            'image'        => $this->image ? url('storage/' . $this->image) : null,
            'scheduled_at' => $this->scheduled_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
        ];
    }
}
