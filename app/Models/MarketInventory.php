<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketInventory extends Model
{
    use HasFactory;

    protected $table = 'market_inventory';
    protected $guarded  = [];
}
