<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Topup extends Model
{
    use HasUuids;
    protected $table = 'topups';
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'amount',
        'balance_before',
        'balance_after',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
