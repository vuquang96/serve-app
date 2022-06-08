<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Csgoroll extends Model
{
    use HasFactory;

    protected $table = 'csgoroll';
    protected $guarded  = [];

    /**
     * get total change
     * @return Number
     */
    public function getTotalChange(){
        $csgoroll = Csgoroll::find($this->id);
        $total = Csgoroll::where('user_request_id', $csgoroll->user_request_id)
                            ->where('full_name', $csgoroll->full_name)
                            ->count();
        return $total;
    }
}
