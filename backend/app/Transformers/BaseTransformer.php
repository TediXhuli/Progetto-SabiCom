<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    public function includeItem($data, $transformer)
    {
        return response()->json(fractal($data, $transformer));
    }

    public function includeCollection($data, $transformer)
    {
        return $this->collection($data, $transformer);
    }

}
