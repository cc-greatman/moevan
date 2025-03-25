<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'google_id',
        'facebook_id',
        'session_id',
        'first_name',
        'last_name',
        'referral_code',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->api_key = 'mepay_' . Str::random(40);
        });
    }

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
        'password' => 'hashed',
    ];

    /**
     * Always encrypt the password when it is updated.
     *
     * @param $value
    * @return string
    */
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }

    protected $appends = ['referral_link'];

    /**
     * Get the user's referral link.
     *
     * @return string
     */
    public function getReferralLinkAttribute() {

        return $this->referral_link = route('user.register.show', ['ref' => $this->referral_code]);
    }

    /**
     * Get email token associated with user
     */
    public function userverify() {

        return $this->hasOne(UserVerify::class);
    }

    public function referrals() {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referee() {
        return $this->hasOne(Referral::class, 'user_id');
    }

    public function Transactions() {

        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }

    public function walletTransactions() {

        return $this->hasMany(WalletTransactions::class, 'user_id', 'id');
    }

    public function dataTransactions() {

        return $this->hasMany(DataTransactions::class, 'user_id');
    }

    public function cableTransactions() {

        return $this->hasMany(cableTransactions::class, 'user_id', 'id');
    }

    public function electricTransactions() {

        return $this->hasMany(ElectricTransaction::class, 'user_id', 'id');
    }

    public function airtimeTransactions() {

        return $this->hasMany(AirtimeTransactions::class, 'user_id', 'id');
    }

    public function monnifyAccounts() {

        return $this->hasMany(MonnifyAccounts::class, 'user_id', 'id');
    }

    public function agentPrices()
{
    return $this->hasMany(AgentDataPlanPrice::class, 'agent_id');
}
}
