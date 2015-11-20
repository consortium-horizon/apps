<?php

namespace App\Http\Controllers;


use App\Http\Requests\NewEventRequest;
use Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\Events;
use App\Paps;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JavaScript;


class ParticipationController extends Controller
{

    public function getNewEvent()
    {
        $members_list = User::lists('name', 'id');
        $userID = Auth::user()->id;
        $userName = Auth::user()->name;
        $userLevel = Auth::user()->userLevel;
        return view('participation/new-event', compact('members_list', 'userName', 'userLevel', 'userID'));
    }

    public function postNewEvent(NewEventRequest $request){
        $input = $request;
        $eventLeadID = $request['eventLead'];
        $user = Auth::user();
        $eventLead = User::where('id', '=', "$eventLeadID")->value('name');
        $Events = new Events();
        $Events -> userID = $user->id;
        $Events -> leadID = $eventLeadID;
        $Events -> eventName = $request['eventName'];
        $Events -> eventType = $request['eventType'];
        $Events -> eventComment = $request['eventComments'];
        $hashedValue = Hash::make($request['eventLead'] . $request['eventName']);
        $Events -> hashedID = $hashedValue;
        $Events -> save();
        $eventName = $request['eventName'];
        $eventPoster = $user->name;
        $papURL = route('memberRegistered',  ['event' => $hashedValue]);

        if($eventLeadID <> $user->id){
            $eventID = Events::where('eventName', '=', $input['eventName'])->value('id');
            $Paps = new Paps();
            $Paps->userID = $eventLeadID;
            $Paps->eventID = $eventID;
            $Paps->save();
            $PapsUser = new Paps();
            $PapsUser->userID = $user->id;
            $PapsUser->eventID = $eventID;
            $PapsUser->save();
            $answer = "<h4>Thank you <strong>{$eventPoster}</strong>!</h4>
                    <h4>The Event of <strong>{$eventLead}</strong>: <strong>{$eventName}</strong> has been successfully registered!</h4>";
            return view('participation/event-registered',compact('eventName', 'eventPoster', 'eventLead', 'papURL', 'answer', 'eventID'));
        }
        else {
            $eventID = Events::where('eventName', '=', $input['eventName'])->value('id');
            $Paps = new Paps();
            $Paps->userID = $eventLeadID;
            $Paps->eventID = $eventID;
            $Paps->save();
            $answer = "<h4>Thank you <strong>{$eventPoster}</strong>!</h4>
                <h4>Your Event <strong>{$eventName}</strong> has been successfully registered!</h4>";
            return view('participation/event-registered',compact('eventName', 'eventPoster', 'eventLead', 'papURL', 'answer', 'eventID'));
        }
    }

    public function registerPap(){

        $input = Request::all();
        $eventHashed = $input['event'];
        $queryEvent = Events::where('hashedID', '=', $eventHashed);
        $query = $queryEvent->get();
        $eventID = $queryEvent->value('id');
        $eventName = $queryEvent->value('eventName');
        $user = Auth::user();
        $queryEventValidation = Paps::where('userID', '=', $user->id, 'and')->where('eventID', '=', $eventID)->get();

        if($queryEventValidation -> isEmpty()) {

            $Paps = new Paps();
            $Paps->userID = $user->id;
            $Paps->eventID = $eventID;
            $Paps->save();

            $answer = "<h4>Your participation to the Event <strong>$eventName</strong> has been successfully Registered!</h4>";

            return view('participation/pap-registered', compact('answer', 'eventName'));
        }
        else {

            $answer = "<h4>Your participation to the Event <strong>$eventName</strong> has already been registered.</h4>";

            return view('participation/pap-registered', compact('answer', 'eventName'));
        }
    }

    // ---------------------  DASHBOARD USERS ---------------------------------------------------
    
    
    // ---------------------- DASHBOARD REFERENTS -------------------------
    public function getAdminDashboard()
    {
        $first = Carbon::create()->startOfMonth();
        $last = Carbon::create()->endOfMonth();
        $userID = Auth::user()->id;
        $userName = Auth::user()->name;

        $papsAllUsers = Paps::groupBy('userID')
                        ->join('users', 'userID', '=', 'users.id')
                        ->where('participation.created_at', '>=', $first)
                        ->where('participation.created_at','<=', $last)
                        ->select('users.name', DB::raw('count(*) as paps'))
                        ->get();
                        
        $papsUser = DB::table('participation')
            -> where('userID', '=', $userID)
            -> lists('eventID');
        $papsTotalUser = DB::table('events')
            -> where('participation.created_at', '>=',$first)
            -> join('participation', function($join){
                $join  -> on('events.id', '=', 'participation.eventID')
                    -> where('participation.userID', '=',  Auth::user()->id);
            })
            -> count();
        $papsUserPvP = DB::table('events')
            -> join('participation', function($join){
                $join   -> on('events.id', '=', 'participation.eventID')
                    -> where('participation.userID', '=',  Auth::user()->id);
            })
            -> where('eventType', '=', 'PvP')
            -> count();
        $papsUserPvE = DB::table('events')
            -> join('participation', function($join){
                $join   -> on('events.id', '=', 'participation.eventID')
                    -> where('participation.userID', '=',  Auth::user()->id);
            })
            -> where('eventType', '=', 'PvE')
            -> count();
        $papsTotalMonth = DB::table('participation')
            -> where('created_at', '>=',$first)
            -> count();
        $papsUserRatio = $papsTotalMonth - $papsTotalUser;
        
        //-------------------------
        

        
        $papsTotalMonthNDay = Paps::where('created_at', '>=', $first)
            ->where('created_at','<=', $last)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(*) as papsTotal')
                ]);
               
        $papsUserMonthNDay = Paps::where('created_at', '>=', $first)
            ->where('created_at','<=', $last)
            ->where('userID', '=', $userID)
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(*) as papsUser')
                ]);

    
       $monthData = [];
       foreach($papsTotalMonthNDay as $k){
         foreach($papsUserMonthNDay as $x){
             if($k->date == $x->date){
                 $monthData[] = ['date' => $k->date, 'papsTotal' => $k->papsTotal, 'papsUser' => $x->papsUser];
             }
         } 
       }

        JavaScript::put([
            'dataSet' => json_encode($papsAllUsers)
        ]);


        return view('participation.pap-dashboard-referents', compact('papsTotalUser', 'papsUserRatio', 'papsUserPvP', 'papsUserPvE', 'monthData'));
    }
}
