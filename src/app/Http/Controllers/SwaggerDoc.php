<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Survey API Denis",
 * description="L5 Swagger OpenApi description",
 * @OA\Contact(
 * email="admin@admin.com"
 * ),
 * )
 *
 * @OA\Server(
 * url="http://localhost:8000",
 * description="API Server"
 * )
 */
class SwaggerDoc
{
    /**
     * @OA\Get(
     * path="/api/surveys",
     * operationId="getSurveysList",
     * tags={"Surveys"},
     * summary="Get list of surveys",
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * ),
     * )
     */
    public function index() {}
}