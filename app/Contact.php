<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Contact
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string $first_name
 * @property ?string $last_name
 * @property ?string $phone
 */
class Contact extends Model
{
    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    protected $fillable = ['first_name', 'last_name', 'email', 'phone'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
