<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(name="Users", description="Quản lý người dùng")
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Danh sách người dùng",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Tạo mới người dùng",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="Nguyen Van A"),
     *              @OA\Property(property="email", type="string", example="nguyenvana@gmail.com"),
     *              @OA\Property(property="password", type="string", example="12345678")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tạo thành công"),
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Chi tiết người dùng",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Thành công"),
     * )
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Không tìm thấy'], 404);

        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Cập nhật người dùng",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Nguyen Van B"),
     *              @OA\Property(property="email", type="string", example="nguyenvana@gmail.com"),
     *              @OA\Property(property="password", type="string", example="12345678")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cập nhật thành công"),
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Không tìm thấy'], 404);

        $user->name  = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Xoá người dùng",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Xoá thành công"),
     * )
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Không tìm thấy'], 404);

        $user->delete();
        return response()->json(null, 204);
    }
}
