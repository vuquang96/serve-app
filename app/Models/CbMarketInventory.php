<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbMarketInventory extends Model
{
    use HasFactory;

    protected $table = 'cb_market_inventory';
    protected $guarded  = [];
}
