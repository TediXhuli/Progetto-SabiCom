<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Users\ChangePasswordRequest;
use App\Http\Requests\Api\Users\CreateUserRequest;
use App\Models\User;
use App\Models\Activity;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UsersController extends ApiController
{
    /**
     * User $user.
     */
    protected $user;

    /**
     * PostsController constructor.
     */
    public function __construct()
    {
        $this->user = auth()
            ->guard('api')
            ->user();
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      operationId="getUsersList",
     *      tags={"Users"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns list of users
     */
    /**
     * @param Request $request
     * @return UsersController
     */
    public function index(Request $request)
    {
        /** @var LengthAwarePaginator $result */
        $users = User::query();

        if ($category = $request->get('category')) {
            $users = $users->whereHas('categories', function ($q) use ($category) {
                $q->where('category_id', $category);
            });
        }

        $result = $this->filterUsers($request)
                       ->paginate($request->get('perPage'));
        return $this->withPaginated($result, new UserTransformer());
    }

    private function filterUsers(Request $request)
    {
        $result = User::query()
                      ->whereHas('roles', function ($q) {
                          $q->where('name', Role::CLIENT);
                      });

        if ($name = $request->get('display_name')) {
            $result = $result->where('display_name', 'like', "$name%");
        }
        if ($search = $request->get('search')) {
            $result = $result->where('email', 'like', "%$search%")
                             ->orWhere('display_name', 'like', "%$search%");
        }
        return $result;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      operationId="getUserById",
     *      tags={"Users"},
     *      summary="Get user information",
     *      description="Returns user data",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={
     *         {
     *             "oauth2_security_example": {"write:users", "read:users"}
     *         }
     *     },
     * )
     */
    /**
     * @param $id
     * @return UsersController
     */
    public function show($id)
    {
        $user = User::query()
                    ->find($id);
        return $this->item($user, new UserTransformer());
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      operationId="createUser",
     *      tags={"Users"},
     *      summary="Create user",
     *      description="Returns 200",
     *      @OA\Parameter(
     *          name="display_name",
     *          description="User Diplay Name",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="User email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="User password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password_confirmation",
     *          description="User password confirmation",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={
     *         {
     *             "oauth2_security_example": {"write:users", "read:users"}
     *         }
     *     },
     * )
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        /** @var User $user */
        $user = User::query()
                    ->create($request->only(['display_name', 'email', 'password']));

        $role = Role::findByName(Role::CLIENT);
        $user->roles()
             ->attach($role);
        return $this->item($user->fresh(), new UserTransformer());
    }

    /**
     * @OA\Put(
     *      path="/api/v1/users/{id}",
     *      operationId="updateUserById",
     *      tags={"Users"},
     *      summary="Update user information",
     *      description="Returns 200",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="display_name",
     *          description="User Display Name",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={
     *         {
     *             "oauth2_security_example": {"write:users", "read:users"}
     *         }
     *     },
     * )
     * @param UpdateUserRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::query()
                    ->findOrFail($id);
        $user->update($request->only(['display_name']));
        return $this->item($user, new UserTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      operationId="deleteUserById",
     *      tags={"Users"},
     *      summary="Delete user",
     *      description="Returns 204",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={
     *         {
     *             "oauth2_security_example": {"write:users", "read:users"}
     *         }
     *     },
     * )
     * @param $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = User::query()
                    ->findOrFail($id);
        try {
            $user->delete();
        } catch (\Exception $e) {
            return $this->wrongArguments([
                'message' => 'Not possible to delete this user for the moment, please contact your system admin!',
            ]);
        }
        return [];
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users/{id}/actions/enable",
     *      operationId="enableUserById",
     *      tags={"Users"},
     *      summary="Enable user",
     *      description="Returns 200",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={
     *         {
     *             "oauth2_security_example": {"write:users", "read:users"}
     *         }
     *     },
     * )
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function enable($id)
    {
        /** @var User $user */
        $user = User::query()
                    ->findOrFail($id);
        $user->enable();
        // $user->notify(new AccountEnabledNotification());
        return $this->item($user, new UserTransformer);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}/actions/disable",
     *      operationId="disableUserById",
     *      tags={"Users"},
     *      summary="Disable user",
     *      description="Returns 200",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={
     *         {
     *             "oauth2_security_example": {"write:users", "read:users"}
     *         }
     *     },
     * )
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function disable($id)
    {
        /** @var User $user */
        $user = User::query()
                    ->findOrFail($id);
        $user->disable();
        // $user->notify(new AccountDisabledNotification());
        return $this->item($user, new UserTransformer);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/changePassword",
     *      operationId="changeLoggedInUserPassword",
     *      tags={"Users"},
     *      summary="Change logged in user's password",
     *      description="Returns user",
     *     @OA\Parameter(
     *          name="password",
     *          description="User password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password_confirmation",
     *          description="User password confirmation",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns list of users
     */
    /**
     * @param ChangePasswordRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(ChangePasswordRequest $request, $id)
    {
        /** @var User $user */
        $user = User::query()
                    ->findOrFail($id);
        $user->updatePassword($request->get('password'));
        return $this->item($user, new UserTransformer);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/actions/getRoles",
     *      operationId="getUsersList",
     *      tags={"Users"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns list of users
     */

    public function updateProfile(UpdateProfileRequest $request)
    {
        /** @var User $user */
        $user = $this->user;
        $user->display_name = $request->get('name');

        $user->save();

        return $this->item($user, new UserTransformer);
    }

    public function addActivity(User $user, Activity $acivity)
    {
        $exits = $user->acivities()
                      ->where('activity_id', $acivity->id)
                      ->exists();

        if ($exits) {
            return $this->item($user->fresh(), new UserTransformer);
        }
        try {
            $user->acivities()
                 ->attach($acivity);
            return $this->item($user->fresh(), new UserTransformer);
        } catch (\Exception $e) {
            return $this->wrongArguments([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function deleteActivity(User $user, Activity $activity)
    {
        try {
            $user->acivities()
                 ->detach($activity);
            return response()->json([], 204);
        } catch (\Exception $exception) {
            return $this->wrongArguments([
                'message' => $exception->getMessage(),
            ]);
        }
    }


}
