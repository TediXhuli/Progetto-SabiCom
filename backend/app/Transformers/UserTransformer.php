<?php

namespace App\Transformers;

use App\Models\User;

class UserTransformer extends BaseTransformer
{

    public function includeActivities(User $user)
    {
        if ($activities = $user->$activities) {
            return $this->collection($activities, new ActivityTransformer(), 'activities');
        }
        return null;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
        ];
    }
}
