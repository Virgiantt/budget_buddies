<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'Transactions';

    protected $primaryKey = 'T_ID';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'T_created_at';
    const UPDATED_AT = 'T_updated_at';

    protected $fillable = [
        'T_ID',
        'T_title',
        'T_type',
        'T_amount',
        'T_transaction_date',
        'Accounts_A_id',
        'Categories_C_id',
    ];

    
public function account()
{
    return $this->belongsTo(Account::class, 'Accounts_A_id', 'A_id');
}

public function category()
{
    return $this->belongsTo(Category::class, 'Categories_C_id', 'C_id');
}

}
