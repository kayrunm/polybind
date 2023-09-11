<?php

namespace Tests\Fixtures\Controllers;

use Illuminate\Http\JsonResponse;

class SingleActionController
{
    public function __invoke($model): JsonResponse
    {
        return response()->json($model);
    }
}
