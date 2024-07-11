<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $date = Carbon::parse($this->created_at);
        return [
            'id' => $this->id,
            'label' => $this->data['label'],
            'message' => $this->data['message'],
            'severity' => $this->data['severity'],
            "send_at" =>  $date->diffForHumans(),
            "is_read" => !!$this->read_at
        ];
    }
}
