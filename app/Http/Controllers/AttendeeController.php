<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\UserEvent;
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


            // get the up coming event status id
            $userEventStatus = UserEventStatus::where('name', 'up coming')->first();

            if (isset($userEventStatus)) {
                $userEvent = UserEvent::where('id', $data['event_id'])->first();
                
                // to avoid malicious requests
                if ($userEvent->is_subscribed === false) {
                    // flage the event as subscribed which means can no longer use it
                    $userEvent->is_subscribed = true;
                    // update event to be up coming
                    $userEvent->user_event_status_id = $userEventStatus->id;
                    // save into databaes
                    $userEvent->save();
                } else {
                    return response()->json(['success' => false, 'message' => 'this event is subscribed'], 200);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'up coming status not found'], 200);
            }

            //todo: send email to host

            //todo: send email to attendee

            return response()->json(['success' => true], 200);

            // todo: send email for the host
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }
}
