<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallets';

    protected $fillable = [
        'wallet', 'secret', 'hexAddress', 'balance', 'bonus', 'locked', 'created_at', 'updated_at'
    ];

}
