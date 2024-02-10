<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $user_id
 * @property string $type
 * @property string $account_no
 * @property int $balance
 * @property Collection $transactions
 * @property User $user
 */
class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'account_no',
        'balance',
    ];

    protected $casts = [
        'balance' => 'int',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'user_id', ownerKey: 'id', relation: 'user');
    }

    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: Transaction::class,
            through: Card::class,
            firstKey: 'account_id',
            secondKey: 'card_id',
            localKey: 'id',
            secondLocalKey: 'id'
        );
    }

    public function cards(): HasMany
    {
        return $this->hasMany(related: Card::class, foreignKey: 'card_id', localKey: 'id');
    }
}
