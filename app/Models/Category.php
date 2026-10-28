<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'Categories';

    protected $primaryKey = 'C_id';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'C_created_at';
    const UPDATED_AT = 'C_updated_at';

    protected $fillable = [
        'C_id',
        'C_name',
        'C_type',
        'Users_U_id',
    ];

    public function user()
    {
        return $this->belongsTo(AuthUser::class, 'Users_U_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'Categories_C_id', 'C_id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'Categories_C_id', 'C_id');
    }
}