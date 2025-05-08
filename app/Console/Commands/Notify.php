<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Booking;

class Notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users about upcoming bookings between 50 and 60 minutes away';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $bookings = DB::table('bookings')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('bookings.notify', 0)
            ->select(
                'bookings.id as booking_id',
                'bookings.start_time',
                'users.id as user_id',
                'users.first_name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'events.id as event_id',
                'events.title as event_title'
            )
            ->get();
//        \Log::info('This gggsrthh command Checking bookings status at ' . now());
        foreach ($bookings as $booking) {
            $startTime = Carbon::parse($booking->start_time);
            $diffInMinutes = $now->diffInMinutes($startTime, false);

            if ($diffInMinutes >= 50 && $diffInMinutes <= 60) {
                $body = "Get ready for the <b>" . $booking->event_title . "</b>, which will start at " . $booking->start_time . ".";

                // Example sendNotification (you need to define this function yourself or replace it)
                sendNotification("+254" . $booking->user_phone, $body);

                // Update notify column using the Booking model
                Booking::where('id', $booking->booking_id)->update(['notify' => 1]);

                $this->info("Notification sent to user {$booking->user_name} for event {$booking->event_title}.");
            }
        }
    }
}
