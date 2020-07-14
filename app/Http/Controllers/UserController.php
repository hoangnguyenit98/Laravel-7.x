<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Validator;

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
        $validator = Validator::make($request->all(), [
            '*.name' => 'required|max:255',
            '*.age' => 'required|max:255',
            '*.email' => 'required|max:255|email|unique:users',
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
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = array_merge($data[$i], ['password' => bcrypt('12345678')]);
        }

        $updateStatus = $this->userService->save($data);

        if ($updateStatus) {
            return response()->json([
                'status' => 'successful',
                'code' => '200',
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
            'age' => 'required|max:255',
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
                'message' => 'xóa user thành công.',
                'payload' => [],
            ]);

        }

        return response()->json([
            'status' => 'unsuccessful',
            'code' => '204',
            'message' => 'xóa user không thành công.',
            'payload' => [],
        ]);
    }
}
