<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendee;
use App\Models\UserEvent;
use App\Mail\SubscribeMail;
use Illuminate\Http\Request;
use App\Models\UserEventStatus;

class AttendeeController extends Controller
{
    /**
     * add attendee to an event
     * 
     * @param Request $request
    */
    public function addAttendee(Request $request)
    {
        try {
            $data = $request->all();
    
            // create new attendee
            Attendee::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'notes' => $data['notes'],
                'user_event_id' => $data['event_id'],
            ]);


            // check user event is found
            $userEvent = UserEvent::where('id', $data['event_id'])->first();
            if (!isset($userEvent)) {
                return response()->json(['success' => false, 'message' => 'this event not found'], 200);
            }
            
            // get the up coming event status id
            // then update the event status from pending to upcoming
            $upcomingEventStatus = UserEventStatus::where('name', 'up coming')->first();
            if (isset($upcomingEventStatus)) {
                // to avoid malicious requests
                if ($userEvent->is_subscribed == false) {
                    // flage the event as subscribed which means can no longer use it
                    $userEvent->is_subscribed = true;
                    // update event to be up coming
                    $userEvent->user_event_status_id = $upcomingEventStatus->id;
                    // save into databaes
                    $userEvent->save();
                } else {
                    return response()->json(['success' => false, 'message' => 'this event is subscribed'], 200);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'up coming status not found'], 200);
            }

            if (env('ALLOW_SUBSCRIPTION_NOTIFICATION', false)) {
                // send subscription mails to host and attendee(invitee)
                $this->sendSubsciptionMails($userEvent);
            }

            return response()->json(['success' => true], 200);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }

    /**
     * send subscription emails for both host and attendee(invitee)
    */
    public function sendSubsciptionMails($userEvent)
    {
        $host = $userEvent->host->toArray();
        $attendee = $userEvent->attendee->toArray();
        $userEvent = $userEvent->toArray();
        // format event date, ex: 19:00 - Friday, 20 January 2023
        $userEvent['date'] = (new Carbon($userEvent['date']))->format('H:i - l, d F Y');
        // send subscription mail to host
        Mail::to($host['email'])->send(new SubscribeMail([
            'host' => $host,
            'fromHost' => true,
            'event' => $userEvent,
            'attendee' => $attendee,
        ]));
        // send subscription mail to attendee
        Mail::to($attendee['email'])->send(new SubscribeMail([
            'host' => $host,
            'fromHost' => false,
            'event' => $userEvent,
            'attendee' => $attendee,
        ]));
        return response()->json(['message' => 'succeeded']);
    }
}
