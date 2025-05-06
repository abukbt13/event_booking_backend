<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class UpdatebookingsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-bookings-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the bookings status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Log to confirm command runs
        \Log::info('Checking bookings status at ' . now());

        // Get all bookings
        $bookings = Booking::where('status','!=','completed')->get();
        $now = \Carbon\Carbon::now();
        foreach ($bookings as $book) {
            if ($book->start_time && $book->end_time) {
                if ($now->between($book->start_time, $book->end_time)) {
                    $book->status = 'active';
                } elseif ($now->gt($book->end_time)) {
                    $book->status = 'completed';
                }
                $book->update();
            } else {
                \Log::warning("Book ID {$book->id} has missing start_time or end_time");
            }
        }
    }

}
