<?php

namespace Tests\Fixtures\Controllers;

use Illuminate\Http\JsonResponse;
use Tests\Fixtures\Models\Comment;
use Tests\Fixtures\Models\HasAuthor;
use Tests\Fixtures\Models\Post;

class BasicController
{
    public function index(): JsonResponse
    {
        return response()->json([]);
    }

    public function show($model): JsonResponse
    {
        return response()->json($model);
    }

    public function showCustom($post): JsonResponse
    {
        return response()->json($post);
    }

    public function showSpecific(Comment $model): JsonResponse
    {
        return response()->json($model);
    }

    public function showUnion(Comment|Post $model): JsonResponse
    {
        return response()->json($model);
    }

    public function showIntersection(Comment&Post $model): JsonResponse
    {
        return response()->json($model);
    }

    public function showInterface(HasAuthor $model): JsonResponse
    {
        return response()->json($model);
    }
}
