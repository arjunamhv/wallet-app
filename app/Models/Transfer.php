<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Transfer extends Model
{
    use HasUuids;
    protected $table = 'transfers';
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'target_user_id',
        'amount',
        'remarks',
        'balance_before',
        'balance_after',
    ];

    public function sender() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id', 'id');
    }
}
