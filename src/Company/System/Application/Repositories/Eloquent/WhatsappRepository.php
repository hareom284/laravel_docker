<?php

namespace Src\Company\System\Application\Repositories\Eloquent;

use GuzzleHttp\Client;
use Src\Company\System\Domain\Repositories\WhatsappRepositoryInterface;

class WhatsappRepository implements WhatsappRepositoryInterface
{
    protected $client;
    protected $accessToken;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->client = new Client();
        $this->accessToken = env('WHATSAPP_ACCESS_TOKEN');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
    }

    public function sendTemplateMessage($template_name, $language_code, $to, $name, $file_url, $document_type, $total_amount)
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
