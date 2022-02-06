<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function item($data, $transformer, $meta = [])
    {
        return response()->json(fractal($data, $transformer)->addMeta($meta));
    }

    public function collection($data, $transformer, $meta = [])
    {
        return response()->json(fractal($data, $transformer)
            ->addMeta($meta)
            ->toArray());
    }

    public function withPaginated($paginator, $transformer, $resourceKey = null, $meta = [])
    {
        return fractal()
            ->collection($paginator->getCollection(), $transformer, $resourceKey)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->addMeta($meta);
    }

    public function wrongArguments($args)
    {
        return response()->json($args, 400);
    }

    public function notFound($args)
    {
        return response()->json($args, 404);
    }

    public function unauthorized($args)
    {
        return response()->json($args, 401);
    }

    public function methodNotAllowed()
    {
        return response()->json([
            'message' => 'This http method is not allowed',
        ], 405);
    }
}
