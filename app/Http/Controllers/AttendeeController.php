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
use Illuminate\Support\Facades\DB;

class AttendeeController extends Controller
{
    /**
     * add attendee to an event
     * 
     * @param Request $request
    */
    public function subscribeToEvent(Request $request)
    {
        $request->validate(['name' => 'required', 'email' => 'email']);
        try {
            $data = $request->all();
    
            DB::beginTransaction();
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
                    // set subscribed_on as the date selected by attenddee
                    $userEvent->subscribed_on = $data['subscribed_on'];
                    // update expire date
                    $userEvent->expire_at = (new Carbon($data['subscribed_on']))->add($userEvent->duration, "minutes");
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

            DB::commit();
            // refresh event after update, eager load host(user) relation
            $userEvent = $userEvent->fresh('host');
            return response()->json(['success' => true, 'data' => $userEvent], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
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
        $userEvent['subscribed_on'] = (new Carbon($userEvent['subscribed_on']))->format('H:i - l, d F Y');
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
