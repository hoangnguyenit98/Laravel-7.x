<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAll($dataSearch)
    {
        $query = User::query();

        if ($dataSearch['name']) {
            $query->where('name', 'like', '%' . $dataSearch['name'] . '%');
        }

        if ($dataSearch['minAge']) {
            $query->where('age', '>=', (int) $dataSearch['minAge']);
        }

        if ($dataSearch['maxAge']) {
            $query->where('age', '<=', (int) $dataSearch['maxAge']);
        }

        if (isset($dataSearch['del_flg'])) {
            $query->where('del_flg', (int) $dataSearch['del_flg']);
        }

        return $query->paginate($dataSearch['limit']);
    }

    public function get($id)
    {
        return User::find($id);
    }

    public function save($data)
    {
        return User::insert($data);
    }

    public function update($id, $data)
    {
        return User::where('id', $id)
            ->update($data);
    }

    public function delete($id)
    {
        return User::where(['id' => $id, 'del_flg' => 0])
            ->update(['del_flg' => 1]);
    }

    public function changePassword($id, $password)
    {
        return User::where('id', $id)
            ->update(['password' => $password]);
    }
}
