<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;
Use DB;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verified', 'verification_token', 'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    /**
     * App\Models\Role relation.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * Verify by token
     *
     * @param $token
     * @return false|User
     */
    public static function verifyByToken($token)
    {
        $user = (new static)->where(['verification_token' => $token, 'verified' => 0])->first();

        if (!$user) {
            return false;
        }

        $user->verify();

        return $user;
    }

    /**
     * Verifiy a user
     *
     * @return bool
     */
    public function verify()
    {
        $this->verification_token = null;
        $this->verified = 1;
        $this->email_verified_at = $this->freshTimestamp();

        return $this->save();
    }

    /**
     * Get user by email
     *
     * @param $email
     * @return User
     */
    public static function byEmail($email)
    {
        return (new static)->where(compact('email'))->first();
    }

    /**
     * Create password recovery token
     */
    public function createPasswordRecoveryToken()
    {
        $token = Str::random(64);

        $created = DB::table('password_resets')->updateOrInsert(
            ['email' => $this->email],
            ['email' => $this->email, 'token' => $token]
        );

        return $created ? $token : false;
    }

    /**
     * Restore password by token
     *
     * @param $token
     * @param $password
     * @return false|User
     */
    public static function newPasswordByResetToken($token, $password)
    {
        $query = DB::table('password_resets')->where(compact('token'));

        $record = $query->first();

        if (!$record) {
            return false;
        }

        $user = self::byEmail($record->email);

        $query->delete();

        return $user->setPassword($password);
    }

    /**
     * Persist a new password for the user
     *
     * @param $password
     * @return bool
     */
    public function setPassword($password)
    {
        $this->password = app('hash')->make($password);;
        return $this->save();
    }
}
