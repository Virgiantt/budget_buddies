<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'Budgets';

    protected $primaryKey = 'B_id';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'B_created_at';
    const UPDATED_AT = 'B_updated_at';

    protected $fillable = [
        'B_id',
        'B_amount_limit',
        'B_month',
        'B_year',
        'Categories_C_id',
    ];

    public function category()
{
    return $this->belongsTo(Category::class, 'Categories_C_id', 'C_id');
}

}
