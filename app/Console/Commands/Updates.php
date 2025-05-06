<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class Updates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
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
                $book->save();
            } else {
                \Log::warning("Book ID {$book->id} has missing start_time or end_time");
            }
        }
    }
}
