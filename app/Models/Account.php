<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'Accounts';

    protected $primaryKey = 'A_id';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'A_created_at';
    const UPDATED_AT = 'A_updated_at';

    protected $fillable = [
        'A_id',
        'A_name',
        'A_account_type',
        'A_initial_balance',
        'Users_U_id',
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'Users_U_id', 'U_id');
}

public function transactions()
{
    return $this->hasMany(Transaction::class, 'Accounts_A_id', 'A_id');
}
}