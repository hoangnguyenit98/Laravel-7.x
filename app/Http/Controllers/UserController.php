<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class UserController extends Controller
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $param = $request->query();

        $dataSearch = [
            'limit' => $param['limit'] ?? 10,
            'name' => $param['name'] ?? '',
            'minAge' => $param['minAge'] ?? '',
            'maxAge' => $param['maxAge'] ?? '',
            'del_flg' => $param['del_flg'] ?? 0,
        ];

        $users = $this->userService->getAll($dataSearch);

        return response()->json([
            'status' => 'successful',
            'code' => '200',
            'message' => 'Lấy dữ liệu thành công.',
            'payload' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            '*.email.unique' => 'Email này đã tồn tại',
        ];

        $validator = Validator::make($request->all(), [
            '*.name' => 'required|max:255',
            '*.age' => 'required|numeric|min:0|max:200',
            '*.email' => 'required|max:255|email|unique:users',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'unsuccessful',
                'code' => '422',
                'message' => 'Dữ liệu không hợp lệ',
                'payload' => ['errors' => $validator->errors()],
            ]);
        }

        $data = $validator->validated();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = array_merge($data[$i], ['password' => bcrypt('12345678')]);
        }

        $updateStatus = $this->userService->save($data);

        if ($updateStatus) {
            return response()->json([
                'status' => 'successful',
                'code' => '201',
                'message' => 'Thêm user thành công.',
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->userService->get($id);

        if ($user) {
            return response()->json([
                'status' => 'successful',
                'code' => '200',
                'message' => 'Lấy thông tin user thành công.',
                'payload' => $user,
            ]);
        }

        return response()->json([
            'status' => 'unsuccessful',
            'code' => '204',
            'message' => 'Không tìm thấy dữ liệu',
            'payload' => [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'age' => 'required|numeric|min:0|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'unsuccessful',
                'code' => '422',
                'message' => 'Dữ liệu không hợp lệ',
                'payload' => ['errors' => $validator->errors()],
            ]);
        }

        $data = $validator->validated();

        $updateStatus = $this->userService->update($id, $data);
        if ($updateStatus) {
            return response()->json([
                'status' => 'successful',
                'code' => '200',
                'message' => 'Chỉnh sửa user thành công.',
                'payload' => [],
            ]);

        }

        return response()->json([
            'status' => 'unsuccessful',
            'code' => '204',
            'message' => 'Chỉnh sửa user không thành công.',
            'payload' => [],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $updateStatus = $this->userService->delete($id);
        if ($updateStatus) {
            return response()->json([
                'status' => 'successful',
                'code' => '200',
                'message' => 'Xóa user thành công.',
                'payload' => [],
            ]);

        }

        return response()->json([
            'status' => 'unsuccessful',
            'code' => '204',
            'message' => 'Xóa user không thành công.',
            'payload' => [],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->all();
        $id = $data['id'];
        $password = Str::random(8);

        $resetPassword = $this->userService->changePassword($id, bcrypt($password));

        if ($resetPassword) {
            $user = $this->userService->get($id);
            $user['password'] = $password;
            \Mail::to($user['email'], 'Reset password')->send(new \App\Mail\ResetPasswordMail($user));
            return response()->json([
                'status' => 'successful',
                'code' => '200',
                'message' => 'Đặt lại mật khẩu thành công. Mật khẩu mới đã được gửi về hòm thư của người dùng.',
                'payload' => [],
            ]);

        }

        return response()->json([
            'status' => 'unsuccessful',
            'code' => '204',
            'message' => 'Đặt lại mật khẩu không thành công.',
            'payload' => [],
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'password_current' => 'required',
            'password' => 'required|confirmed|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'unsuccessful',
                'code' => '422',
                'message' => "Dữ liệu không hợp lệ.",
                'payload' => ['errors' => $validator->errors()],
            ]);
        }

        $data = $validator->validated();
        if (\Hash::check($data['password_current'], auth()->user()->password)) {
            $result = $this->userService->changePassword($data['id'], bcrypt($data['password']));
            if ($result) {
                return response()->json([
                    'status' => 'successful',
                    'code' => '200',
                    'message' => 'Đổi mật khẩu thành công.',
                    'payload' => [],
                ]);

            }
        }

        return response()->json([
            'status' => 'unsuccessful',
            'code' => '204',
            'message' => 'Đổi mật khẩu không thành công.',
            'payload' => [],
        ]);
    }

    public function exportCsv(Request $request)
    {
        return Excel::download(new UsersExport, 'users.xlsx');
        // $param = $request->query();

        // $dataSearch = [
        //     'limit' => $param['limit'] ?? 10,
        //     'name' => $param['name'] ?? '',
        //     'minAge' => $param['minAge'] ?? '',
        //     'maxAge' => $param['maxAge'] ?? '',
        //     'del_flg' => $param['del_flg'] ?? 0,
        // ];

        // $users = $this->userService->getAll($dataSearch);

        // return response()->json([
        //     'status' => 'successful',
        //     'code' => '200',
        //     'message' => 'Lấy dữ liệu thành công.',
        //     'payload' => $users,
        // ]);
    }
}
