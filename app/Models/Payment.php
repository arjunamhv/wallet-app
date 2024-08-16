<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'amount',
        'remarks',
        'balance_before',
        'balance_after',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
