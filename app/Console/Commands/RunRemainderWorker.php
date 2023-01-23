<?php

namespace App\Console\Commands;

use Mail;
use Carbon\Carbon;
use App\Models\UserEvent;
use App\Mail\RemainderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunRemainderWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:notifyWorker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command run the worker to check every minute are there events to notify it';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notNotifiedEvents = 
            UserEvent::where('is_notified', false)->where('is_subscribed', true)->get();
        foreach ($notNotifiedEvents as $event) {
            // while getting now() is before in egypt by 2 Hours(GMT+00)
            $afterAnHour = (new Carbon(now(), 'EET'))->addMinutes(60);
            // date stored as in request but with GMT+00,
            // so to make exact diff between now and event date want to make both GMT+02
            // in the meanwhile keep the event date with it's stored value but update GMT+00 to GMT+02
            $eventDate = new Carbon($event->date->format('Y-m-d H:i:s'), 'EET');
            if ($eventDate->isBefore($afterAnHour) || $eventDate->equalTo($afterAnHour)) {
                $host = $event->host->toArray();
                $eventArray = $event->toArray();
                $attendee = $event->attendee->toArray();
                // format event date, ex: 19:00 - Friday, 20 January 2023
                $eventArray['date'] = (new Carbon($eventArray['date']))->format('H:i - l, d F Y');
                // send subscription mail to host
                Mail::to($host['email'])->send(new RemainderMail([
                    'host' => $host,
                    'event' => $eventArray,
                    'attendee' => $attendee,
                ]));
                // send subscription mail to attendee
                Mail::to($attendee['email'])->send(new RemainderMail([
                    'host' => $host,
                    'event' => $eventArray,
                    'attendee' => $attendee,
                ]));
                $event->is_notified = true;
                $event->save();
            }
        }
    }
}
