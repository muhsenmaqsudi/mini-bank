<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $account_id
 * @property string $card_no
 * @property Account $account
 */
class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'card_no',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(related: Account::class, foreignKey: 'account_id', ownerKey: 'id', relation: 'account');
    }
}
