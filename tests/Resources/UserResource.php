<?php

namespace F9Web\ApiResponseHelpers\Tests\Resources;

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
            'nameAndAge' => $this->name . ' & ' . $this->age,
        ];
    }
}