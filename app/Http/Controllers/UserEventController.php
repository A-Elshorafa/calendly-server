<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\UserEvent;
use Illuminate\Http\Request;
use App\Models\UserEventStatus;
use Illuminate\Support\Facades\DB;
use App\Models\UserEventAvailableDates;
use App\Models\UserEventAvailableTimes;

class UserEventController extends Controller
{
    /**
     * store new user event
     * 
     * @param Request $request
    */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        try {
            $data = $request->all();
            $pendingEvent = UserEventStatus::where('name', 'pending')->first();
    
            if (isset($pendingEvent)) {
                $lastDateIndex = count($data['available_dates_times']) - 1;
                $lastTimeIndex = count($data['available_dates_times'][$lastDateIndex]['times']) - 1;
                $date = $data['available_dates_times'][$lastDateIndex]['date'];
                $time = $data['available_dates_times'][$lastDateIndex]['times'][$lastTimeIndex];
                $expiryDate = (new Carbon($date . $time));
                DB::beginTransaction();
                // create user event with the request data
                $createdEvent = UserEvent::create([
                    'name' => $data['name'],
                    'expire_at' => $expiryDate,
                    'agenda' => $data['agenda'],
                    'user_id' => $data['user_id'],
                    'duration' => $data['duration'],
                    'user_event_status_id' => $pendingEvent->id,
                    'third_party_name' => $data['third_party_name'],
                ]);

                /**
                 * Create the available date then inject available date id to times data
                 * then apply insert one time
                 *
                 * sample of availble dates and times data
                 * [{
                 *   date: "dd-mm-yyyy",
                 *   times: ["time-1", "time-2", "time-3"]
                 * }]
                */
                foreach ($data['available_dates_times'] as $pair) {
                    $availableDate = UserEventAvailableDates::create([
                        'date' => $pair['date'],
                        'user_event_id' => $createdEvent->id
                    ]);

                    // extract times per date
                    $availableTimes = array_map(function($time) use ($availableDate) {
                        $time = [
                            'time' => $time,
                            'user_event_available_date_id' => $availableDate->id
                        ];
                        return $time;
                    }, $pair['times']);

                    // apply mass assertion
                    UserEventAvailableTimes::insert($availableTimes);
                }

                // customize the local url
                // ex:// localhost:8000/{event_id}/{customized_part}
                $calendlyLink =
                    env('FRONTEND_URL') .'/event-subscription/' . $createdEvent->id . '/' . $data['customized_url'];
                $createdEvent->update([
                    'calendly_link' => $calendlyLink
                ]);

                DB::commit();
                return response()->json(['success' => true, 'calendly_link' => $calendlyLink], 200);
            }

            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'invalid status name'], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }

    /**
     * get event info by event id
     * 
     * @param Request $request
    */
    public function getPendingEventDetails(Request $request)
    {
        try {
            $data = $request->all();

            // get pending status model
            $pendingStatus = UserEventStatus::where('name', 'pending')->first();
            // get user event by id
            $userEvent = UserEvent::with(['host', 'attendee'])->where('id', $data['event_id'])->first();
            if ($userEvent['is_subscribed']) {
                return response()->json(['success' => true, 'data' => null, 'is_subscribed' => true], 200);
            }

            return response()->json(['success' => true, 'data' => $userEvent], 200);
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }

    /**
     * get event info by event id
     * 
     * @param Request $request
    */
    public function getUpcomingEventDetails(Request $request)
    {
        try {
            $data = $request->all();

            // get upcoming status model
            $upcomingStatus = UserEventStatus::where('name', 'up coming')->first();
            
            // get user event by id
            $userEvent = UserEvent::with(['host', 'attendee'])->where('id', $data['event_id'])
                ->where('user_event_status_id', $upcomingStatus->id)->first();

            if(!isset($userEvent)) {
                return response()->json(['success' => false, 'data' => null , 'message' => 'Event not found'], 200);
            }
            return response()->json(['success' => true, 'data' => $userEvent], 200);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }

    /**
     * return upcoming events
     * 
     * @param Request $request
    */
    public function getUpComingEvents(Request $request)
    {
        try {
            // get up coming event status id
            $upComingStatus = UserEventStatus::where('name', 'up coming')->first();

            $userId = $request->get('user_id');
            // get up coming events by status id
            $upComingEvents = 
                UserEvent::with('attendee')->where('user_event_status_id', $upComingStatus->id)
                    ->where('user_id', $userId)->get()->toArray();

            return response()->json(['success' => true, 'data' => $upComingEvents], 200);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }

    /**
     * return pending events
     * 
     * @param Request $request
    */
    public function getPendingEvents(Request $request)
    {
        try {
            // get the user id
            $userId = $request->get('user_id');

            // get pending event status id
            $pendingStatus = UserEventStatus::where('name', 'pending')->first();

            // get pending events by status id
            $pendingEvents = UserEvent::where('user_event_status_id', $pendingStatus->id)
                ->where('user_id', $userId)->get()->toArray();

            return response()->json(['success' => true, 'data' => $pendingEvents], 200);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }

    public function deleteEvent(Request $request)
    {
        $request->validate(['event_id'=> 'required']);
        try {
            if ($request->has('event_id')) {
                $eventId = $request->get('event_id');
                $userEvent = UserEvent::where('id', $eventId)->first();
                $pendingEventStatus = UserEventStatus::where('name', 'pending')->first();
                if (isset($userEvent)) {
                    if (!$userEvent['is_subscribed'] && $userEvent['user_event_status_id'] === $pendingEventStatus['id']) {
                        $userEvent->delete();
                        return response()->json(['message' => 'deleted successfully'], 200);
                    }
                    return response()->json(['message' => 'event deletion only allowed for pending events'], 200);
                }
                return response()->json(['message' => 'event not found'], 200);
            }
        } catch (\Exception $ex) {
            return response()->json(['message' => 'An error occurred while deleting the event'], 500);
        }
    }

    /**
     * update event notes shown only by host
     * 
     * @param Request $request
    */
    public function updateEventNotes(Request $request)
    {
        try {
            // get the user id
            $notes = $request->get('notes');
            $eventId = $request->get('event_id');

            // get pending events by status id
            $userEvent = UserEvent::where('id', $eventId)->first();

            if (!isset($userEvent)) {
                return response()->json(['success' => false, 'message' => 'event not found'], 200);
            }
            $userEvent->update(['notes' => $notes]);

            return response()->json(['success' => true, 'message' => 'event updated succesfully'], 200);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'messsage'=>'server error'], 500);
        }
    }
}
