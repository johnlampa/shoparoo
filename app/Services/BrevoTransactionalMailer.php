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
        if (!is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('Brevo API key is not configured.');
        }

        $baseUrl = config('services.brevo.base_url', 'https://api.brevo.com/v3/');
        $timeout = (int) config('services.brevo.timeout', 15);

        $client = new Client([
            'base_uri' => rtrim($baseUrl, '/') . '/',
            'timeout' => $timeout,
        ]);

        $client->post('smtp/email', [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $apiKey,
                'content-type' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => config('mail.from.name'),
                    'email' => config('mail.from.address'),
                ],
                'to' => [[
                    'email' => $toEmail,
                    'name' => $toName,
                ]],
                'subject' => $subject,
                'htmlContent' => $htmlContent,
            ],
        ]);
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
