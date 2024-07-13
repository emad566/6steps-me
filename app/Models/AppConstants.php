<?php

namespace App\Models;

abstract class AppConstants
{
    static public $PerPage = 50;

    static public $listVaidations = [
        'paginationCounter' => 'nullable|numeric|min:1|max:1000',
        'page' => 'nullable|numeric|min:1|max:1000',
        'sortDirection' => 'nullable|in:ASC,DESC',
        'dateFrom' => 'nullable|date_format:Y-m-d H:i:s',
        'dateTo' => 'nullable|date_format:Y-m-d H:i:s',
        
    ];
}
