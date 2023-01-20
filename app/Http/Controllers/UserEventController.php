<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\UserEvent;
use Illuminate\Http\Request;
use App\Models\UserEventStatus;

class UserEventController extends Controller
{
    /**
     * store new user event
     * 
     * @param Request $request
    */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $eventStatus = UserEventStatus::where('name', $data['status'])->first();
    
            if (isset($eventStatus) && $eventStatus->id > 0) {
                // create user event with the request data
                $createdEvent = UserEvent::create([
                    'name' => $data['name'],
                    'user_id' => $data['user_id'], // todo: get authenticated user id
                    'duration' => $data['duration'],
                    'password' => $data['password'],
                    'user_event_status_id' => $eventStatus->id,
                    'date' => (new Carbon)->parse($data['date']),
                    'third_party_link' => $data['third_party_link'],
                    'third_party_name' => $data['third_party_name'],
                ]);

                // customize the local url
                // ex:// localhost:8000/{event_id}/{customized_part}
                $createdEvent->update([
                    'calendly_link' => env('FRONTEND_URL') . '/' . $createdEvent->id . '/' . $data['customized_url']
                ]);

                return response()->json(['success' => true], 200);
            }

            return response()->json(['success' => false, 'message' => 'invalid status name'], 200);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }

    /**
     * store new user event
     * 
     * @param Request $request
    */
    public function getEventInfo(Request $request)
    {
        try {
            $data = $request->all();
    
            // get user event by id
            $userEvent = UserEvent::where('id', $data['event_id'])->first()->toArray();

            return response()->json(['success' => true, 'data' => $userEvent], 200);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }
}
