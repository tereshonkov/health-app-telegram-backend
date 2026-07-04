<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeasureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'systolic'  => $this->systolic,
            'diastolic' => $this->diastolic,
            'pulse'     => $this->pulse,
            'note'      => $this->note,
            'date' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
