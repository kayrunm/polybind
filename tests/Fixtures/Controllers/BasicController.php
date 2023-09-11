<?php

namespace Tests\Fixtures\Controllers;

use Illuminate\Http\JsonResponse;

class BasicController
{
    public function show($model): JsonResponse
    {
        return response()->json($model);
    }
}
