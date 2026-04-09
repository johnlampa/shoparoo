<h2>Verify your email address</h2>

<p>Hi {{ $user->name }},</p>

<p>Thanks for signing up for {{ config('app.name') }}. Please verify your email address by clicking the link below.</p>

<p>
    <a href="{{ $verificationUrl }}">Verify Email Address</a>
</p>

<p>If you did not create an account, you can ignore this email.</p>
