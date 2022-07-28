<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class H5pRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'playlist_id' => $this->playlist_id,
            'activity_id' => $this->activity_id,
            'statement' => $this->statement,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}