<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRolesEnum;
use App\Events\CreatingUser;
use App\Events\UserCreated;
use App\Notifications\PasswordChangeRequiredNotification;
use App\Notifications\ResetPasswordNotification;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        // 'password' => 'hashed',
    ];

    public function getUserData()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "roles" => $this->roles()->get(['name'])->map(fn($role) => $role->name)
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $clientBaseUrl = config('frontend_urls.reset_password');
        $url = "{$clientBaseUrl}?token={$token}";
        $this->notify(new ResetPasswordNotification($this->name, $url));
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return str_ends_with($this->email, '@troloppe.com') && $this->hasRole('Admin');
    }
    public static function booted()
    {
        static::creating(function (User $user) {
            if ($user->email !== 'paschal.okafor@troloppe.com') {
                event(new CreatingUser($user));
                $user->password = Hash::make($user->password);
            }
        });
        static::created(function (User $user) {
            if ($user->email !== 'paschal.okafor@troloppe.com') {
                event(new UserCreated($user));
            }
        });
    }

    public function streetData(): HasMany
    {
        return $this->hasMany(StreetData::class, 'creator_id');
    }

    public function isUpline(): bool
    {
        return $this->hasAnyRole([UserRolesEnum::RESEARCH_MANAGER->value, UserRolesEnum::ADMIN->value]);
    }

    public static function verifyByEmail(string $email)
    {
        try {
            User::whereEmail($email)->firstOrFail();
        } catch (\Exception $e) {
            throw new ModelNotFoundException('Email not found on our database.');
        }

    }
}
