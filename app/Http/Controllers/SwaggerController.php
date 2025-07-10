<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="User Management API",
 *     version="1.0.0",
 *     description="API hệ thống quản lý người dùng sử dụng Laravel và JWT"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Server API chính"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class SwaggerController extends Controller
{
    //
}
