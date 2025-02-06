<?php

use App\Models\Log;

function storelog($user_id,$title, $description) {
    Log::create([
        'user_id' => $user_id,
        'title' => $title,
        'description' => $description,
        'date' => Carbon\Carbon::now()->toDateString(),
        'time' => Carbon\Carbon::now()->toTimeString(),
    ]);

}
