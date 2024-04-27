<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApiController extends Controller
{
    public function getAllImages(): JsonResponse
    {
        return new JsonResponse(Image::all(), ResponseAlias::HTTP_OK);
    }

    public function getImage(int $id): JsonResponse
    {
        $image = Image::query()->find($id);

        if ($image === null) {
            return new JsonResponse([], ResponseAlias::HTTP_NOT_FOUND);
        }

        return new JsonResponse([$image], ResponseAlias::HTTP_OK);
    }
}
