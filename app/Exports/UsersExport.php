<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class UsersExport implements FromArray
{

    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    function array(): array
    {
        return $this->data;
    }
}
