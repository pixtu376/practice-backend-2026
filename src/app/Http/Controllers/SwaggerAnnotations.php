<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * title="Survey API",
 * version="1.0.0"
 * )
 */
class SwaggerAnnotations
{
    /**
     * @OA\Get(
     * path="/api/test-swagger",
     * summary="Тестовый эндпоинт",
     * @OA\Response(response=200, description="OK")
     * )
     */
    public function test() {}
}