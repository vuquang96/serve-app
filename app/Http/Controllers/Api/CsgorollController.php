<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Csgoroll;
use App\Models\UserRequest;
use App\Models\Buff;
use DB;
use Carbon\Carbon;

class CsgorollController extends Controller
{
    public function update(Request $request){
        DB::beginTransaction();
        try {
            $data               = $request->all();
                  
            $data_csgoroll      = json_decode($data['data_csgoroll'], true);
            $userRequestID      = $data['user_request_id'];

            $userRequest = UserRequest::find($userRequestID);
            if($userRequest->on_change) {
                sleep(90);
            }

            $this->startOnChange($userRequestID);

            $conversionPrice    = $data['conversion_price'];
            if(is_null($conversionPrice)) {
                $conversionPrice = 370;
            }
               
            $this->saveData($data_csgoroll, $userRequestID, $conversionPrice);
            
            $this->endOnChange($userRequestID);

            DB::commit();
            return response()->json([
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error'
            ]);
        }
    }

    private function getBuff($buffs, $name){
        foreach($buffs as $item) {
            if($item->name == $name) {
                return $item;
            }
        }
        return null;
    }

    private function formatTime($time){
        $date       = explode(' ', $time);
        $date_tmp   = explode('/', $date[0]);
        $fullDate   = $date_tmp[2] . '-' . $date_tmp[1] . '-' . $date_tmp[0] . ' ' . $date[1];

        return $fullDate;
    }

    private function saveData($data, $userRequestID, $conversionPrice){
        if(is_array($data) && count($data)) {
            $buffs = Buff::all();
            $dataInsert = [];
            foreach($data as $item) {
                if(count($item['price_asyn'])) {
                    $buff               = $this->getBuff($buffs, $item['full_name']);
                    $priceBuff          = null;
                    $priceBuffOrigin    = null;
                    if($buff) {
                        $priceBuff          = $buff->sell_min_price * $conversionPrice;
                        $priceBuffOrigin    = $buff->sell_min_price;
                    }
                    foreach($item['price_asyn'] as $itemPrice) {
                        $time = $this->formatTime($itemPrice['time']);
                        $checkCsgo = Csgoroll::where('user_request_id', $userRequestID)
                                                ->where('full_name', $item['full_name'])
                                                ->where('created_at', $time)
                                                ->first();
                        if(!$checkCsgo) {
                            $csgorollPrice = $this->productBilling(
                                                    (double)$itemPrice['price_difference'], 
                                                    (double)str_replace(',', '', $itemPrice['price'])
                                                );
                            $tmp = [
                                'full_name'                 => $item['full_name'],
                                'user_request_id'           => $userRequestID,
                                'price_difference'          => $itemPrice['price_difference'],
                                'conversion_price_buff'     => $conversionPrice,
                                'csgoroll'                  => $csgorollPrice,
                                'buff'                      => $priceBuff,
                                'created_at'                => $time,
                                'rate'                      => '',
                            ];
                            if(!is_null($priceBuff)) {
                                $tmp['rate'] = number_format($priceBuff/$csgorollPrice, 2);
                            }
                            $dataInsert[] = $tmp;
                        }
                    }
                }
            }
             
            if(count($dataInsert) > 0) {
                $dataInsertChunk = array_chunk($dataInsert, 100);
                foreach($dataInsertChunk as $item) {
                    Csgoroll::insert($item);
                }
            }
        }
    }

    private function productBilling($partialValue, $totalValue) {
        return $totalValue - (($partialValue / 100) * $totalValue);
    } 

    private function startOnChange($id){
        $userRequest = UserRequest::find($id);
        $userRequest->time_end = Carbon::now();
        $userRequest->on_change = true;
        $userRequest->save();
    }

    private function endOnChange($id){
        $userRequest = UserRequest::find($id);
        $userRequest->time_end = Carbon::now();
        $userRequest->on_change = false;
        $userRequest->save();
    }
}
