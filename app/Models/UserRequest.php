<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory;
    protected $table = 'user_request';
    protected $guarded  = [];

    /**
     * get stock order
     * @return Number
     */
    public function getTotalProduct(){
        $total = Csgoroll::where('user_request_id', $this->id)->count();
        return $total;
    }
}
