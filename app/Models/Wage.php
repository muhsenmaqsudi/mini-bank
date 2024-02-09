<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $txn_id
 * @property string $fee
 */
class Wage extends Model
{
    use HasFactory;

    protected $fillable = [
        'txn_id',
        'fee',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(related: Transaction::class, foreignKey: 'txn_id', ownerKey: 'id', relation: 'transaction');
    }
}
