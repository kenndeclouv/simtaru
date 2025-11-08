<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
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
            'event' => $this->event,
            'description' => $this->description,
            'timestamp' => $this->created_at->toDateTimeString(),
            'causer' => new UserResource($this->whenLoaded('causer')),

            'changes' => $this->when(

                $this->event === 'updated' && $this->properties->has('old'),
                fn () => $this->formatChanges($this->properties['old'], $this->properties['attributes'])
            ),

            'properties' => $this->when(
                $this->event !== 'updated' && $this->properties->count() > 0,
                fn () => $this->properties
            ),
        ];
    }

    /**
     * Helper buat ngubah data 'old' & 'new' jadi array yg rapi.
     */
    private function formatChanges($old, $new)
    {
        $changes = [];
        foreach ($new as $key => $newValue) {

            if (in_array($key, ['updated_at', 'created_at', 'user_request_tte_id', 'request_tte_date', 'approved_date'])) {
                continue;
            }

            $oldValue = $old[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[] = [

                    'field' => ucwords(str_replace(['var_', 'enum_', 'text_'], '', str_replace('_', ' ', $key))),
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }
        return $changes;
    }
}
