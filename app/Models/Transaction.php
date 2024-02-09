<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $track_id
 * @property int $card_id
 * @property string $type
 * @property boolean $is_refunded
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_id',
        'card_id',
        'type',
        'amount',
        'is_refunded',
    ];

    public function wage(): HasOne
    {
        return $this->hasOne(related: Wage::class, foreignKey: 'txn_id', localKey: 'id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(related: Transfer::class, foreignKey: 'txn_id', localKey: 'id');
    }
}
