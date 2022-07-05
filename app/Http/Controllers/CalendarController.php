<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CalendarController extends Controller
{
    protected $client;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/google-calendar/client_secret.json'));
        $client->setRedirectUri('http://localhost:8912/login/google/callback');
        $client->addScope('profile');
        $client->addScope(Google_Service_Calendar::CALENDAR);

        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);

           

            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';

            $results = $service->events->listEvents($calendarId);

            $events =  $results->getItems();

            $dataArr = [];

            foreach ($events as $key => $event) {
                $temp_Arr = [
                    'id' => $event->id,
                    'title' => $event->getSummary(),
                    'start' => $event->getStart()->getDateTime(),
                    'end' => $event->getEnd()->getDateTime(),
                ];
                array_push($dataArr, $temp_Arr);
            }

            return view('calendar.index', compact('dataArr'));
        } else {
            return redirect()->route('oauth');
        }
    }

    public function oauth()
    {
        session_start();


        if (!isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
            return redirect($filtered_url);
        } else {
            $this->client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
            
            

            return redirect()->route('index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('calendar.create-event');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        session_start();
        $startDateTime = $request->start_date . ':00+05:30';
        $endDateTime = $request->end_date . ':00+05:30';
        // dd($startDateTime);
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';
            $event = new Google_Service_Calendar_Event([
                'summary' => $request->title,
                'description' => $request->description,
                'start' => [
                    'dateTime' => $startDateTime,

                ],
                'end' => [
                    'dateTime' => $endDateTime,

                ],

            ]);
            $results = $service->events->insert($calendarId, $event);

            if (!$results) {
                return redirect()->back()->with('alert-error', 'Something went wrong');
                // return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return redirect()->route('index')->with('alert-success', 'Event Created Successfully');
            // return response()->json(['status' => 'success', 'message' => 'Event Created']);
        } else {
            return redirect()->route('oauth');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($eventId = 0)
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);

            $service = new Google_Service_Calendar($this->client);
            dd($service->events->listEvents('primary'));
            $event = $service->events->get('primary', $eventId);

            if (!$event) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $event]);
        } else {
            return redirect()->route('oauth');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, $eventId)
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $startDateTime = Carbon::parse($request->start_date)->toRfc3339String();

            $eventDuration = 30; //minutes

            if ($request->has('end_date')) {
                $endDateTime = Carbon::parse($request->end_date)->toRfc3339String();
            } else {
                $endDateTime = Carbon::parse($request->start_date)->addMinutes($eventDuration)->toRfc3339String();
            }

            // retrieve the event from the API.
            $event = $service->events->get('primary', $eventId);

            $event->setSummary($request->title);

            $event->setDescription($request->description);

            //start time
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startDateTime);
            $event->setStart($start);

            //end time
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($endDateTime);
            $event->setEnd($end);

            $updatedEvent = $service->events->update('primary', $event->getId(), $event);


            if (!$updatedEvent) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $updatedEvent]);
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($eventId)
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $service->events->delete('primary', $eventId);
        } else {
            return redirect()->route('oauthCallback');
        }
    }
}
























// {


//     public function create()
//     {
//         return view('calendar.create-event');
//     }


//     public function store(Request $request)
//     {
//         session_start();

//         $user = Socialite::driver('google')->stateless()->user();

//         // Set token for the Google API PHP Client
//         $google_client_token = [
//             'access_token' => $user->token,
//             'refresh_token' => $user->refreshToken,
//             'expires_in' => $user->expiresIn
//         ];

//         $client = new Google_Client();
//         $client->setApplicationName("Laravel-Calendar");
//         $client->setDeveloperKey(env('GOOGLE_SERVER_KEY'));
//         $client->setAccessToken(json_encode($google_client_token));
//         $client->setScopes(config('services.google.scopes'));
    

//         // $guzzleclient = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
//         // $guzzleclient->setDefaultOption('headers/code', $_SESSION['token']);
//         // $client->setHttpClient($guzzleclient);

//         // $client->setAccessToken($_SESSION['token']);


//         $service = new Google_Service_Calendar($client);

//         $startDateTime = $request->start_date;
//         $endDateTime = $request->end_date;

//         $calendarId = 'primary';
//         $event = new Google_Service_Calendar_Event([
//             'summary' => $request->title,
//             'description' => $request->description,
//             'start' => ['dateTime' => $startDateTime],
//             'end' => ['dateTime' => $endDateTime],

//         ]);



//         $results = $service->events->insert($calendarId, $event);

//         if (!$results) {
//             return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
//         }

//         return response()->json(['status' => 'success', 'message' => 'Event Created']);
//     }




//     /**
//      * Display the specified resource.
//      *
//      * @param $eventId
//      * @return \Illuminate\Http\Response
//      * @internal param int $id
//      */
//     public function show($eventId)
//     {
//         session_start();
//         if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//             $this->client->setAccessToken($_SESSION['access_token']);

//             $service = new Google_Service_Calendar($this->client);
//             $event = $service->events->get('primary', $eventId);

//             if (!$event) {
//                 return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
//             }
//             return response()->json(['status' => 'success', 'data' => $event]);
//         } else {
//             return redirect()->route('oauthCallback');
//         }
//     }

//     /**
//      * Show the form for editing the specified resource.
//      *
//      * @param  int $id
//      * @return \Illuminate\Http\Response
//      */
//     public function edit($id)
//     {
//         //
//     }

//     /**
//      * Update the specified resource in storage.
//      *
//      * @param  \Illuminate\Http\Request $request
//      * @param $eventId
//      * @return \Illuminate\Http\Response
//      * @internal param int $id
//      */
//     public function update(Request $request, $eventId)
//     {
//         session_start();
//         if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//             $this->client->setAccessToken($_SESSION['access_token']);
//             $service = new Google_Service_Calendar($this->client);

//             $startDateTime = Carbon::parse($request->start_date)->toRfc3339String();

//             $eventDuration = 30; //minutes

//             if ($request->has('end_date')) {
//                 $endDateTime = Carbon::parse($request->end_date)->toRfc3339String();
//             } else {
//                 $endDateTime = Carbon::parse($request->start_date)->addMinutes($eventDuration)->toRfc3339String();
//             }

//             // retrieve the event from the API.
//             $event = $service->events->get('primary', $eventId);

//             $event->setSummary($request->title);

//             $event->setDescription($request->description);

//             //start time
//             $start = new Google_Service_Calendar_EventDateTime();
//             $start->setDateTime($startDateTime);
//             $event->setStart($start);

//             //end time
//             $end = new Google_Service_Calendar_EventDateTime();
//             $end->setDateTime($endDateTime);
//             $event->setEnd($end);

//             $updatedEvent = $service->events->update('primary', $event->getId(), $event);


//             if (!$updatedEvent) {
//                 return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
//             }
//             return response()->json(['status' => 'success', 'data' => $updatedEvent]);
//         } else {
//             return redirect()->route('oauthCallback');
//         }
//     }

//     /**
//      * Remove the specified resource from storage.
//      *
//      * @param $eventId
//      * @return \Illuminate\Http\Response
//      * @internal param int $id
//      */
//     public function destroy($eventId)
//     {
//         session_start();
//         if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//             $this->client->setAccessToken($_SESSION['access_token']);
//             $service = new Google_Service_Calendar($this->client);

//             $service->events->delete('primary', $eventId);
//         } else {
//             return redirect()->route('oauthCallback');
//         }
//     }
// }