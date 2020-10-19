<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'avatar' => is_null($this->avatar) ? url('default_images/avatar.jpg') : url("avatars/{$this->avatar}"),
            'username' => $this->username,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
