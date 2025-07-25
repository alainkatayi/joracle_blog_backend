<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        Carbon::setLocale('fr');
        return [
            'id'=> $this->id,
            'title' => $this->title,
            'content'=>$this->content,
            'slug'=>$this->slug,
            'photo'=>$this->photo,
            'created_at'=>Carbon::parse($this->created_at)->diffForHumans(),
            'user_id'=> new UserResource($this->user)
        ];
    }
}
