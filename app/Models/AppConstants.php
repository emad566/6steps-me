<?php

namespace App\Models;

abstract class AppConstants
{
    static public $PerPage = 50;
    static public $otpDelay = 3;

    static public $listVaidations = [
        'paginationCounter' => 'nullable|numeric|min:1|max:1000',
        'page' => 'nullable|numeric|min:1|max:1000',
        'sortDirection' => 'nullable|in:ASC,DESC',
        'dateFrom' => 'nullable|date_format:Y-m-d H:i:s',
        'dateTo' => 'nullable|date_format:Y-m-d H:i:s', 
    ];

    static public $campain_states = [
        'UnderReview', 'Rejected', 'Active', 'Complete', 'Stoped', 'Ended', 
    ]; 

    static public $request_states = [
        'RequestRecieved', 'RequestAccepted', 'RequestRejected', 'RequestCompleted', 'CampaignCompleted'
    ]; 

    static public $video_states = [
        'VideoRecieved', 'VideoAccepted', 'VideoRejected'
    ]; 
    
}
