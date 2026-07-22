<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Mail\Mailable;
use RuntimeException;

class BrevoTransactionalMailer
{
    public function sendHtml(string $toEmail, ?string $toName, string $subject, string $htmlContent): void
    {
        $apiKey = config('services.brevo.api_key');
        if (!is_string($apiKey) || trim($apiKey) === '') {
            throw new RuntimeException('Brevo API key is not configured.');
        }

        $fromAddress = config('mail.from.address');
        if (!is_string($fromAddress) || trim($fromAddress) === '' || str_contains($fromAddress, 'example.com')) {
            throw new RuntimeException('MAIL_FROM_ADDRESS must be a verified Brevo sender address.');
        }

        $baseUrl = config('services.brevo.base_url', 'https://api.brevo.com/v3/');
        $timeout = (int) config('services.brevo.timeout', 15);

        $client = new Client([
            'base_uri' => rtrim($baseUrl, '/') . '/',
            'timeout' => $timeout,
        ]);

        try {
            $client->post('smtp/email', [
                'headers' => [
                    'accept' => 'application/json',
                    'api-key' => trim($apiKey),
                    'content-type' => 'application/json',
                ],
                'json' => [
                    'sender' => [
                        'name' => config('mail.from.name') ?: config('app.name'),
                        'email' => $fromAddress,
                    ],
                    'to' => [[
                        'email' => $toEmail,
                        'name' => $toName,
                    ]],
                    'subject' => $subject,
                    'htmlContent' => $htmlContent,
                ],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            throw new RuntimeException('Brevo send failed: '.$body, previous: $e);
        }
    }

    public function sendMailable(string $toEmail, ?string $toName, Mailable $mailable): void
    {
        $builtMailable = method_exists($mailable, 'build') ? $mailable->build() : $mailable;
        $subject = is_string($builtMailable->subject) && $builtMailable->subject !== ''
            ? $builtMailable->subject
            : config('app.name');

        $this->sendHtml($toEmail, $toName, $subject, $builtMailable->render());
    }
}
