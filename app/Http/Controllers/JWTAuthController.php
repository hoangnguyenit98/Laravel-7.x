<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class JWTAuthController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth.api', ['except' => ['login', 'register']]);
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'age' => 'required|numeric|min:0|max:200',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|string|min:8',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = "Dữ liệu không hợp lệ.";
            if ($errors->first('email') && $errors->first('email') == "The email has already been taken.") {
                $message = "Email đã tồn tại.";
            }
            return response()->json([
                'status' => 'unsuccessful',
                'code' => '422',
                'message' => $message,
                'payload' => ['errors' => $validator->errors()],
            ]);
        }

        $moreData = [
            'password' => bcrypt($request->password),
            'created_at' => Carbon::now(),
        ];
        $data = array_merge(
            $validator->validated(),
            $moreData
        );
        $result = $this->userService->save($data);

        if ($result) {
            return response()->json([
                'status' => 'successful',
                'code' => '201',
                'message' => 'Đăng ký thành công',
                'payload' => [],
            ]);
        }

        return response()->json([
            'status' => 'unsuccessful',
            'code' => '204',
            'message' => 'Thêm user không thành công.',
            'payload' => [],
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'unsuccessful',
                'code' => '422',
                'message' => 'Dữ liệu không hợp lệ',
                'payload' => ['errors' => $validator->errors()],
            ]);
        }

        if (!$token = auth()->attempt(array_merge($validator->validated(), ['del_flg' => 0]))) {
            return response()->json([
                'status' => 'unsuccessful',
                'code' => '401',
                'message' => 'Tài khoản hoặc mật khẩu không chính xác.',
                'payload' => [],
            ]);
        }

        return response()->json([
            'status' => 'successful',
            'code' => '200',
            'message' => 'Đăng nhập thành công',
            'payload' => ['user' => auth()->user()],
        ])->withCookie('token', $token, auth()->factory()->getTTL(), "/", null, false, true);
    }

    public function profile()
    {
        return response()->json([
            'status' => 'successful',
            'code' => '200',
            'message' => 'Lấy thông tin tài khoản thành công',
            'payload' => ['user' => auth()->user()],
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => 'successful',
            'code' => '200',
            'message' => 'Đăng xuất thành công',
            'payload' => ['user' => auth()->user()],
        ]);
    }

    public function refresh()
    {
        $token = auth()->refresh();
        return response()->json([
            'status' => 'successful',
            'code' => '200',
            'message' => 'Làm mới token thành công',
            'payload' => ['user' => auth()->user()],
        ])->withCookie('token', $token, auth()->factory()->getTTL(), "/", null, false, true);
    }
}
