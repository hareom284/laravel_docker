<?php

namespace Src\Company\Notification\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\Notification\Domain\Services\FirebaseNotification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Notifications\DatabaseNotification;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 120;
    public $tries = 1; 

    protected $title;
    protected $message;
    protected $user_ids;
    protected $deviceId;
    protected $sender;
    protected $projectId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $message, $deviceId, $projectId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->deviceId = $deviceId;
        $this->projectId = $projectId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            Log::channel('daily')->info($this->title);
            Log::channel('daily')->info($this->message);
            Log::channel('daily')->info($this->deviceId);

            // $accessToken = env('FIREBASE_AUTH_TOKEN');
            // $fireBaseProject = env('FIREBASE_PROJECT');

            // $url = "https://fcm.googleapis.com/v1/projects/{$fireBaseProject}/messages:send";

            // Log::channel('daily')->info($accessToken);
            // Log::channel('daily')->info($fireBaseProject);
            // Log::channel('daily')->info($url);

            $user = UserEloquentModel::query()->where('device_id',$this->deviceId)->first();

            $params = [
                'title' => $this->title,
                'message' => $this->message,
                'projectId' => $this->projectId
            ];

            $user->notify((new FirebaseNotification($params)));

            $latestNoti = DatabaseNotification::where('type', FirebaseNotification::class)->where(function ($query) use ($user) {
                $query->where('notifiable_id', $user->id);
            })->orderBy('created_at', 'desc')->first();

            $companyName = env('COMPANY_FOLDER_NAME');

            $filePath = storage_path('app/firebase/'.$companyName.'-firebase.json');

            Log::channel('daily')->info($filePath);

            $firebase = (new Factory)
            ->withServiceAccount($filePath);

            Log::channel('daily')->info(json_encode($firebase));

            $messaging = $firebase->createMessaging();

            Log::channel('daily')->info(json_encode($messaging));

            $message = CloudMessage::new()->toToken($this->deviceId) // FCM device token
            ->withNotification([
                'title' => $this->title,
                'body' => $this->message,
            ])
            ->withData([
                "route" => "/project/project-detail", // Route you want to navigate to
                "id" => (string)$this->projectId, // Optional additional data
                "notiId" => $latestNoti->id
            ]);

            Log::channel('daily')->info(json_encode($message));

            $response = $messaging->send($message);

            // $payload = [
            //     "message" => [
            //         "token" => $this->deviceId,
            //         "notification" => [
            //             "title" => $this->title,
            //             "body" => $this->message,
            //         ],
            //         "android" => [
            //             "priority" => "high"
            //         ],
            //         "data" => [
            //             "route" => "/project/project-detail", // Route you want to navigate to
            //             "id" => (string)$this->projectId // Optional additional data
            //         ]
            //     ]
            // ];

            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer ' . $accessToken,
            //     'Content-Type' => 'application/json'
            // ])->post($url,$payload);

            Log::channel('daily')->info(json_encode($response));

            logger('Sent Push Notification');

        } catch (\Exception $ex) {
            logger(['error', $ex]);
        }
    }
}
