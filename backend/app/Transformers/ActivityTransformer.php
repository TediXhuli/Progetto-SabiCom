<?php

namespace App\Transformers;

use App\Models\Activity;

class ActivityTransformer extends BaseTransformer
{
    /**
     * @param Activity $activity
     *
     * @return array
     */
    public function transform(Activity $activity)
    {
        return [
            'id'          => $activity->id,
            'name'        => $activity->name,
            'description' => $activity->description,
            'status'      => $activity->status,
            'user_id'     => $activity->user_id,
            'created_at'     => $activity->created_at,
            'deleted_at'     => $activity->deleted_at
        ];
    }
}
