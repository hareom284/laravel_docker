<?php

namespace Src\Company\System\Domain\Channels;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class WhatsappChannel
{
    protected $client;
    protected $accessToken;
    protected $phoneNumberId;

    public function __construct()
    {
        $whatsapp_access_token = GeneralSettingEloquentModel::where('setting', 'whatsapp_access_token')->first();
        $whatsapp_phone_number_id = GeneralSettingEloquentModel::where('setting', 'whatsapp_phone_number_id')->first();

        $decrypted_token = "";
        if ($whatsapp_access_token) {
            $decrypted_token = Crypt::decryptString($whatsapp_access_token->value);
        }
        else{
            $decrypted_token = $whatsapp_access_token;
        }
        $this->client = new Client();
        $this->accessToken = $decrypted_token ? $decrypted_token : null;
        $this->phoneNumberId = $whatsapp_phone_number_id ? $whatsapp_phone_number_id->value : null;
    }

    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toWhatsapp')) {
            $messageData = $notification->toWhatsapp($notifiable);
            if ($this->accessToken && $this->phoneNumberId) {
                return $this->sendTemplateMessage(
                    $messageData['template_name'],
                    $messageData['language_code'],
                    $messageData['to'],
                    $messageData['components']
                );
            }
        }
    }

    public function sendTemplateMessage($template_name, $language_code, $to, $components = [])
    {
        $url = "https://graph.facebook.com/v20.0/{$this->phoneNumberId}/messages";

        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $template_name,
                    'language' => [
                        'code' => $language_code
                    ],
                    'components' => $components
                    // 'components' => [
                    //     [
                    //         'type' => 'header',
                    //         'parameters' => [
                    //             [
                    //                 'type' => 'document',
                    //                 'document' => [
                    //                     'link' => $file_url,
                    //                     'filename' => basename($file_url)
                    //                 ]
                    //             ]
                    //         ],
                    //     ],
                    //     [
                    //         'type' => 'body',
                    //         'parameters' => [
                    //             [
                    //                 'type' => 'text',
                    //                 'text' => $name
                    //             ],
                    //             [
                    //                 'type' => 'text',
                    //                 'text' => $document_type
                    //             ],
                    //             [
                    //                 'type' => 'text',
                    //                 'text' => $total_amount
                    //             ]
                    //         ],
                    //     ],
                    // ],
                ],
            ];

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$this->accessToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp template message: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send template message: ' . $e->getMessage(),
            ];
        }
    }
}
