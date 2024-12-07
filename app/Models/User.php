<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use LaravelAndVueJS\Traits\LaravelPermissionToVueJS;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LaravelPermissionToVueJS, SoftDeletes;

    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'lastname',
        'middle_initial',
        'date_of_birth',
        'contact_number',
        'telephone_number',
        'reason_of_disapproval',
        'photos',
        'approved_at',
        'user_photo',
        'latitude',
        'longitude',
        'qrcode'
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

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class);
    // }
    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class);
    // }

    // public function roles()
    // {
    //     return $this->hasManyThrough(Role::class, User::class, 'id', 'id');
    // }

    // public function permissions()
    // {
    //     return $this->roles()->pluck('permissions')->flatten(); // Get all permissions from all roles (optional)
    // }
    public function jsPermissions()
    {
        return json_encode([
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ]);
    }

    public function deliquencies(): HasMany
    {
        return $this->hasMany(Deliquency::class, 'committed_by');
    }
}
