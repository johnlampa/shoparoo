<h2>Reset your password</h2>

<p>Hi {{ $user->name }},</p>

<p>We received a request to reset your password for {{ config('app.name') }}.</p>

<p>
    <a href="{{ $resetUrl }}">Reset Password</a>
</p>

<p>If you did not request a password reset, you can ignore this email.</p>
