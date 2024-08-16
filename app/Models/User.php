<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Model implements Authenticatable
{
    use HasUuids;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    public $timestamps = true;
    protected $fillable = ['first_name', 'last_name', 'phone_number', 'pin', 'balance', 'address', 'token'];

    public function getAuthIdentifierName(): string
    {
        return 'phone_number';
    }
    public function getAuthIdentifier(): string
    {
        return $this->phone_number;
    }
    public function getAuthPassword(): string
    {
        return $this->pin;
    }
    public function getAuthPasswordName(): string
    {
        return 'pin';
    }
    public function getRememberToken(): string
    {
        return $this->token;
    }
    public function setRememberToken($value): void
    {
        $this->token = $value;
    }
    public function getRememberTokenName(): string
    {
        return 'token';
    }
}
