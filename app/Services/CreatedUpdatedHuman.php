<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class CreatedUpdatedHuman
{ 
    public $human = [];
    public function __construct($item)
    { 
        $this->human = [ 
            'created_at_human' => Carbon::parse($item->created_at)->diffForHumans(),
            'updated_at_human' => Carbon::parse($item->updated_at)->diffForHumans(),
            'deleted_at_human' => !$item->deleted_at? null :  Carbon::parse($item->deleted_at)->diffForHumans(),
        ];
    }
 
}
