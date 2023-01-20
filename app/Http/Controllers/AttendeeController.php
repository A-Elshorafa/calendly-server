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


            // update user event status from pending -> up coming
            $userEventStatus = UserEventStatus::where('name', 'up coming')->first();

            if (isset($userEventStatus)) {
                UserEvent::where('id', $data['event_id'])
                    ->update(["user_event_status_id" => $userEventStatus->id]);
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
