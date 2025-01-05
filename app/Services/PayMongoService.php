<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayMongoService
{
    protected $secretKey;
    protected $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function createCheckoutSession(array $params)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->secretKey),
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/checkout_sessions', [
            'data' => [
                'attributes' => [
                    'line_items' => [
                        [
                            'amount' => $params['amount'],
                            'currency' => $params['currency'],
                            'name' => 'Property Registration Fee',
                            'quantity' => 1,
                        ]
                    ],
                    'payment_method_types' => ['card', 'gcash', 'grab_pay'],
                    'send_email_receipt' => true,
                    'show_description' => true,
                    'show_line_items' => true,
                    'description' => $params['description'],
                    'success_url' => route('filament.admin.pages.property-registration'),
                    'cancel_url' => route('filament.admin.pages.property-registration')
                ]
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('PayMongo API Error: ' . $response->body());
        }

        $data = $response->json()['data'];
        return (object)[
            'id' => $data['id'],
            'checkout_url' => $data['attributes']['checkout_url']
        ];
    }
}

