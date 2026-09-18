<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayPalService
{
    public function getAccessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth(
                config('services.paypal.client_id'),
                config('services.paypal.client_secret')
            )
            ->post(
                config('services.paypal.base_url') .
                '/v1/oauth2/token',
                [
                    'grant_type' => 'client_credentials',
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Unable to get PayPal access token: ' .
                $response->body()
            );
        }

        return $response->json('access_token');
    }

    public function createPayout(
        string $receiver,
        string $amount,
        string $currency,
        string $senderBatchId,
        string $senderItemId,
        string $note
    ): array {
        $token = $this->getAccessToken();

        $response = Http::baseUrl(
            config('services.paypal.base_url')
        )
            ->withToken($token)
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders([
                'PayPal-Request-Id' => $senderBatchId,
            ])
            ->post('/v1/payments/payouts', [
                'sender_batch_header' => [
                    'sender_batch_id' => $senderBatchId,
                    'email_subject' => 'Supplier Payment',
                ],

                'items' => [
                    [
                        'recipient_type' => 'EMAIL',
                        'receiver' => $receiver,

                        'amount' => [
                            'value' => number_format(
                                (float) $amount,
                                2,
                                '.',
                                ''
                            ),
                            'currency' => $currency,
                        ],

                        'note' => $note,

                        'sender_item_id' => $senderItemId,
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'PayPal payout failed: ' .
                $response->body()
            );
        }

        return $response->json();
    }

    public function getPayout(string $payoutBatchId): array
    {
        $token = $this->getAccessToken();

        $response = Http::baseUrl(
            config('services.paypal.base_url')
        )
            ->withToken($token)
            ->acceptJson()
            ->get(
                "/v1/payments/payouts/{$payoutBatchId}"
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Unable to get payout status: ' .
                $response->body()
            );
        }

        return $response->json();
    }
}