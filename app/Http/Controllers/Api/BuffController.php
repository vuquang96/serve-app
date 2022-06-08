<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buff;
use App\Models\UserRequest;
use DB;
use Carbon\Carbon;

class BuffController extends Controller
{
    public function update(Request $request){
        DB::beginTransaction();
        try {
            $data               = $request->all();
            $dataBuffStore      = json_decode($data['data_buff_store_asyn'], true);
            $init               = $data['init'];
            $userRequestID      = $data['user_request_id'];
            
            if($init) {
                $userRequest = new UserRequest;
                $userRequest->time_start = Carbon::now();
                $userRequest->time_end = Carbon::now();
                $userRequest->save();
                $userRequestID = $userRequest->id;
            } else {
                $userRequest = UserRequest::find($userRequestID);
                if($userRequest->on_change) {
                    sleep(90);
                }
                $userRequest->on_change = true;
                $userRequest->save();
            }
            
           // DB::statement('TRUNCATE TABLE buff');
            
            Buff::whereNotNull('id')->delete();

            $dataInsert = [];
            if(count($dataBuffStore) > 0) {
                foreach($dataBuffStore as $item) {
                    $tmp = [
                        'buff_id'           => $item['id'],
                        'name'              => $item['name'],
                        'sell_min_price'    => (double)$item['sell_min_price'],
                        'buy_max_price'     => (double)$item['buy_max_price'],
                        'created_at'        => Carbon::now(),
                        'updated_at'        => Carbon::now(),
                    ];
                    $dataInsert[] = $tmp;
                }
                 
                $dataInsertChunk = array_chunk($dataInsert, 100);
                
                foreach($dataInsertChunk as $item) {
                    Buff::insert($item);
                }
            }
            $this->cancelOnChange($userRequestID);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'user_request_id' => $userRequestID,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error'
            ]);
        }
    }

    private function cancelOnChange($id){
        $userRequest = UserRequest::find($id);
        $userRequest->on_change = false;
        $userRequest->save();
    }
}
