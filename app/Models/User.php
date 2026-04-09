<?php

namespace App\Models;

use App\Services\BrevoTransactionalMailer;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        app(BrevoTransactionalMailer::class)->sendHtml(
            $this->email,
            $this->name,
            'Verify Email Address',
            view('mail.verify-email', [
                'user' => $this,
                'verificationUrl' => $this->verificationUrl(),
            ])->render(),
        );
    }

    public function sendPasswordResetNotification($token): void
    {
        app(BrevoTransactionalMailer::class)->sendHtml(
            $this->email,
            $this->name,
            'Reset Password Notification',
            view('mail.reset-password', [
                'user' => $this,
                'resetUrl' => $this->passwordResetUrl($token),
            ])->render(),
        );
    }

    protected function verificationUrl(): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ],
        );
    }

    protected function passwordResetUrl(string $token): string
    {
        return url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));
    }
}
