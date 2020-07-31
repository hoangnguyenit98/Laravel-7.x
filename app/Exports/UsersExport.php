<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class UsersExport implements FromArray
{
    function array(): array
    {
        return [[1, 2, 3], [1, 2, 3]];
    }
}
