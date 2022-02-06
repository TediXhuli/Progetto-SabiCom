<?php

namespace App\Http\Controllers\Api;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivitiesController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $user = auth()
                ->guard('api')
                ->user();

            if (!$user) {
                return response()->json(['error' => 'Utente non autorizzato'], 401);
            }

            $activities = Activity::query()
                ->where('user_id', $user->id)
                ->get()
                ->toArray();

            return $activities;
        } catch (\Exception $exception) {
            return $this->wrongArguments([
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function show(Request $request, $activityId)
    {
        try {
            $activity= Activity::find($activityId);

            $user = auth()
                ->guard('api')
                ->user();

            if ($user->id != $activity->user_id) {
                return response()->json(['error' => 'Questa attivita non e assgnata a questo utente!'], 401);
            }
            return $activity;
        } catch (\Exception $exception) {
            return $this->wrongArguments([
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth()
                ->guard('api')
                ->user();

            if (!$user) {
                return response()->json(['error' => 'Questa attivita non e assgnata a questo utente!'], 401);
            }

            $data = $request->only([
                'name',
                'description',
                'status',
            ]);

            $activity = Activity::createItem($data);

            return $activity;
        } catch (\Exception $exception) {
            return $this->wrongArguments([
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $activityId)
    {
        try {
            $activity= Activity::find($activityId);

            $user = auth()
                ->guard('api')
                ->user();   

            if ($user->id != $activity->user_id) {
                return response()->json(['error' => 'Questa attivita non e assgnata a questo utente!'], 401);
            }

            $data = $request->only([
                'name',
                'description',
                'status',
                'user_id',
            ]);
            $activity->updateItem($data);
            return $activity;
        } catch (\Exception $exception) {
            return $this->wrongArguments([
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request, $activityId)
    {
        try {
            $activity= Activity::find($activityId);

            $user = auth()
                ->guard('api')
                ->user();

            if ($user->id != $activity->user_id) {
                return response()->json(['error' => 'Questa attivita non e assgnata a questo utente!'], 401);
            }

            $activity->deleted_at = now();

            $activity->update();
            return response()->json($activity, 204);
        } catch (\Exception $exception) {
            return $this->wrongArguments([
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
