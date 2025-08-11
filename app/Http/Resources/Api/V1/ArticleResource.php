<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'summary'      => $this->summary,
            'body'         => $this->when($request->user()?->can('articles.read_full'), $this->body),
            'cover_url'    => $this->cover_path ? asset('storage/'.$this->cover_path) : null,
            'status'       => $this->status,
            'published_at' => $this->published_at,
            'reading_time' => $this->reading_time,
            'language'     => $this->language,
            'category'     => new CategoryResource($this->whenLoaded('category')),
            'tags'         => TagResource::collection($this->whenLoaded('tags')),
            'author'       => $this->whenLoaded('author', fn()=>[
                'id'=>$this->author->id,'name'=>$this->author->name
            ]),
            'created_at'   => $this->created_at,
        ];
    }
}
