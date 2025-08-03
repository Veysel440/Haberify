<?php

namespace App\Http\Resources\News;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Tag\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'excerpt'     => $this->excerpt,
            'content'     => $this->content,
            'image'       => $this->image ? url('storage/' . $this->image) : null,
            'category'    => new CategoryResource($this->whenLoaded('category')),
            'tags'        => TagResource::collection($this->whenLoaded('tags')),
            'created_at'  => $this->created_at?->toDateTimeString(),
            'scheduled_at'=> $this->scheduled_at?->toDateTimeString(),
            'gallery'     => NewsImageResource::collection($this->whenLoaded('images')),
            'video'       => $this->video,
        ];
    }
}
