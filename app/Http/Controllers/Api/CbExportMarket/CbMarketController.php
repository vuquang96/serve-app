<?php

namespace App\Http\Controllers\Api\CbExportMarket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CbMarketBuff;
use App\Models\CbMarketCsgoroll;
use App\Models\CbMarketInventory;
use DB;
use Carbon\Carbon;

class CbMarketController extends Controller
{
    
    public function buff(Request $request){
        CbMarketBuff::truncate();

        $data = $request->all();
        $dataStore      = json_decode($data['data_store'], true);

        foreach($dataStore as $item) {
            $tmp = [
                'name'              => $item['name'],
                'price'             => (double)$item['price'],
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];
            $dataInsert[] = $tmp;
        }
         
        $dataInsertChunk = array_chunk($dataInsert, 200);
        
        foreach($dataInsertChunk as $item) {
            CbMarketBuff::insert($item);
        }
        return 1;
    }


    public function csgoroll(Request $request){
        CbMarketCsgoroll::truncate();

        $data = $request->all();
        $dataStore      = json_decode($data['data_store'], true);

        foreach($dataStore as $item) {
            $tmp = [
                'name'              => $item['name'],
                'price'             => (double)$item['price'],
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];
            $dataInsert[] = $tmp;
        }
         
        $dataInsertChunk = array_chunk($dataInsert, 200);
        
        foreach($dataInsertChunk as $item) {
            CbMarketCsgoroll::insert($item);
        }
        return 1;
    }

    public function listInventory(Request $request){
        $data                   = $request->all();
        $priceMin               = $data['price_min'];
        $priceMax               = $data['price_max'];

        $this->setInventory($priceMin, $priceMax);

        $marketInventory = CbMarketInventory::select(['name'])->get();
        return $marketInventory->toArray();
    }

    public function downloadData(Request $request){
        $data = $request->all();
        $rateBuff               = $data['buff'];
        $rateCsgoroll           = $data['csgoroll'];
        $rate_bonus_csgoroll    = $data['rate_bonus_csgoroll'];
        $rateBonusCsgoroll      = (100 + (double)$rate_bonus_csgoroll) / 100;

        $marketInventory = CbMarketInventory::all();

        foreach($marketInventory as $item){
            $name       = $item->name;
            $csgoroll   = CbMarketCsgoroll::where('name', $name)
                                        ->selectRaw('name, price, CAST(price as DECIMAL(9,2)) _price')
                                        ->orderBy('_price', 'ASC')->first();
            $buff       = CbMarketBuff::where('name', $name)
                                        ->selectRaw('name, price, CAST(price as DECIMAL(9,2)) _price')
                                        ->orderBy('_price', 'ASC')->first();
            $item->rate = null;
            $priceBuff      = 0;
            $priceCsgoroll  = 0;

            // buff
            if($buff) {
                $priceBuff = (double)$buff->price * (double)$rateBuff;

                $item->buff = $this->formatPrice($buff->price, $priceBuff);
                $item->buff_rate = $priceBuff;
                $item->buff_sort = (double)$buff->price;
            } else {
                $item->buff_rate = 0;
            }

            // csgoroll
            if($csgoroll) {
                if($csgoroll->price != 0) {
                    $priceCsgoroll = ((double)$csgoroll->price * $rateBonusCsgoroll)  * (double)$rateCsgoroll;
               
                    $item->csgoroll_rate = $priceCsgoroll;
                    $item->csgoroll = $this->formatPrice($csgoroll->price, $priceCsgoroll);
                } else {
                    $item->csgoroll = '';
                    $item->csgoroll_rate = 0;
                }
            }

            if($priceBuff != 0 && $priceCsgoroll != 0) {
                if($priceBuff < $priceCsgoroll) {
                    /*$priceBuffRate = (double)$buff->price * (double)$rateBuff;
                    $priceCsgorollRate = (double)$csgoroll->price  * (double)$rateCsgoroll;
                    $item->rate = ($priceCsgorollRate / $priceBuffRate) * 100;
                    $item->rate = $item->rate - 100;*/

                    $item->rate = ($priceCsgoroll / $priceBuff) * 100;
                    $item->rate = $item->rate - 100;
                }
            }

            $item->save();
        }

        $data = CbMarketInventory::select('name', 'buff', 'csgoroll', 'rate')
                            ->orderBy('rate', 'DESC')
                            ->orderBy('buff_sort', 'DESC')
                            ->get()->toArray();
        return $data;
    }

    private function formatPrice($priceBefore, $priceAfter){
        return number_format((double)$priceBefore, 2) . ' (' . number_format($priceAfter, 2) . ')';
    }

    private function setInventory($priceMin, $priceMax) {
        CbMarketInventory::truncate();

        $arrayName          = [];
        $dataInsert         = [];

        $buffs              = CbMarketBuff::where(DB::raw('CAST(price as DECIMAL(9,2))'), '>=', $priceMin)
                                ->where(DB::raw('CAST(price as DECIMAL(9,2))'), '<=', $priceMax)
                               // ->skip(0)->take(20)
                                ->selectRaw('name, price, CAST(price as DECIMAL(9,2)) _price')
                                ->get();

        if($buffs) {
            foreach($buffs as $item) {
                if(!array_search($item->name, $arrayName)) {
                    $tmp = [
                        'name'              => $item->name,
                        'buff'              => (double)$item->price,
                        'created_at'        => Carbon::now(),
                        'updated_at'        => Carbon::now(),
                    ];

                    $dataInsert[] = $tmp;

                    $arrayName[] = $item->name;
                }
            }

            $dataInsertChunk = array_chunk($dataInsert, 200);
            
            foreach($dataInsertChunk as $item) {
                CbMarketInventory::insert($item);
            }
        }
    }
}
