<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'Users';

    protected $primaryKey = 'U_id';

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'U_created_at';
    const UPDATED_AT = 'U_updated_at';

    protected $fillable = [
        'U_id',
        'U_name',
        'U_email',
        'U_password',
    ];

    protected $hidden = [
        'U_password',
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class, 'Users_U_id', 'U_id');
    }
}
