<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buff;
use App\Models\UserRequest;
use App\Models\Csgoroll;
use DB;
use Carbon\Carbon;
use App\Models\CbMarketBuff;
use App\Models\CbMarketCsgoroll;
use App\Models\CbMarketInventory;


class ProductController extends Controller
{
    public function userRequest(){
        $userRequest = UserRequest::orderBy('time_start', 'DESC')->paginate(50);
        return view('product.user_request')->with(['userRequest' => $userRequest]);
    }

    public function list($requestID, Request $request){
        $csgoroll = Csgoroll::where('user_request_id', $requestID)
                            ->orderBy('created_at', 'DESC')
                            ->get();
         
        return view('product.list')->with(['csgoroll' => $csgoroll, 'requestID' => $requestID]);
    }

    public function destroy($requestID) {
        $userRequest = UserRequest::where('id', $requestID)->first();
        if($userRequest) {
            UserRequest::where('id', $requestID)->delete();
            Csgoroll::where('user_request_id', $requestID)->delete();
        }
        return redirect(route(('product.request')));
    }

    public function detail($requestID, Request $request){
        $name = $request->input('name', '');
               
        $csgoroll = Csgoroll::where('full_name', $name)
                                    ->where('user_request_id', $requestID)
                                    ->orderBy('created_at', 'DESC')
                                    ->get();
        
        return view('product.detail')->with(['csgoroll' => $csgoroll]);
    }

    public function buffCsgoroll(Request $request)
    {
        $rateBuff               = $data['buff'] ?? 3.7;
        $rateCsgoroll           = $data['csgoroll'] ?? 14.5;
        $inventory = $this->getInventory($request);

        return view('buff_csgoroll.list', compact('rateBuff', 'rateCsgoroll', 'inventory'));
    }

    private function getInventory(Request $request){
        $data = $request->all();
        $rateBuff               = $data['buff'] ?? 3.7;
        $rateCsgoroll           = $data['csgoroll'] ?? 14.5;

        $marketInventory = CbMarketInventory::all();

        foreach($marketInventory as $item){
            $name       = $item->name;
            $csgoroll   = CbMarketCsgoroll::where('name', $name)
                                        ->selectRaw('name, price, CAST(price as DECIMAL(9,2)) _price')
                                        ->orderBy('_price', 'ASC')->first();
            $buff       = CbMarketBuff::where('name', $name)
                                        ->selectRaw('name, price, CAST(price as DECIMAL(9,2)) _price')
                                        ->orderBy('_price', 'ASC')->first();
            $max        = 0;
            $min        = 0;

            // csgoroll
            if($csgoroll) {
                if($csgoroll->price != 0) {
                    $priceCsgoroll = (double)$csgoroll->price  * (double)$rateCsgoroll;
                
                    /*if($priceCsgoroll > $max) {
                        $max = $priceCsgoroll;
                        $item->tick = 'csgoroll';
                    }*/

                    if($priceCsgoroll < $min) {
                        $min = $priceCsgoroll;
                        $item->tick = 'csgoroll';
                    }

                    $item->csgoroll = $this->formatPrice($csgoroll->price, $priceCsgoroll);
                } else {
                    $item->csgoroll = '';
                }
            }

            // buff
            if($buff) {
                $priceBuff = (double)$buff->price * (double)$rateBuff;

                /*if($priceBuff > $max) {
                    $max = $priceBuff;
                    $item->tick = 'buff';
                }*/

                if($priceBuff < $min) {
                    $min = $priceBuff;
                    $item->tick = 'buff';
                }

                $item->buff = $this->formatPrice($buff->price, $priceBuff);
                $item->buff_sort = (double)$buff->price;
            }

         
            $item->save();
        }

        $data = CbMarketInventory::select('name', 'buff', 'csgoroll', 'tick')
                            ->orderBy('buff_sort', 'DESC')
                            ->get();
        return $data;
    }

    private function formatPrice($priceBefore, $priceAfter){
        return number_format((double)$priceBefore, 2) . ' (' . number_format($priceAfter, 2) . ')';
    }
}
