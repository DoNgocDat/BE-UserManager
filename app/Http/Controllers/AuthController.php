<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Đăng ký người dùng mới",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="Nguyen Van A"),
     *              @OA\Property(property="email", type="string", example="nguyenvana@gmail.com"),
     *              @OA\Property(property="password", type="string", example="12345678")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Đăng ký thành công"),
     * )
     */
    public function register(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($data->fails()) {
            return response()->json($data->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User registered', 'user' => $user]);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Đăng nhập và nhận token JWT",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", example="nguyenvana@example.com"),
     *              @OA\Property(property="password", type="string", example="12345678")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Đăng nhập thành công"),
     *     @OA\Response(response=401, description="Sai thông tin đăng nhập")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json(['token' => $token]);
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     tags={"Authentication"},
     *     summary="Lấy thông tin người dùng hiện tại (qua token)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Thông tin người dùng")
     * )
     */
    public function me()
    {
        return response()->json(JWTAuth::parseToken()->authenticate());
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Đăng xuất (vô hiệu hóa token)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Đăng xuất thành công")
     * )
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

}