<?php

namespace App\Http\Controllers\Api\ExportMarket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketBuff;
use App\Models\MarketCsgoempire;
use App\Models\MarketCsgoroll;
use App\Models\MarketInventory;
use DB;
use Carbon\Carbon;

class MarketController extends Controller
{
    public function inventory(Request $request){
        MarketInventory::truncate();

        $data = $request->all();
        $dataStore      = json_decode($data['data_store'], true);

        foreach($dataStore as $item) {
            $tmp = [
                'name'              => $item['name'],
                'csgoempire_default'=> (double)$item['price']/100,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];
            $dataInsert[] = $tmp;
        }
         
        $dataInsertChunk = array_chunk($dataInsert, 200);
        
        foreach($dataInsertChunk as $item) {
            MarketInventory::insert($item);
        }
        return 1;
    }
    
    public function buff(Request $request){
        MarketBuff::truncate();

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
            MarketBuff::insert($item);
        }
        return 1;
    }

    public function csgoempire(Request $request){
        MarketCsgoempire::truncate();

        $data = $request->all();
        $dataStore      = json_decode($data['data_store'], true);

        foreach($dataStore as $item) {
            $tmp = [
                'name'              => $item['name'],
                'price'             => (double)$item['price']/100,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];
            $dataInsert[] = $tmp;
        }
         
        $dataInsertChunk = array_chunk($dataInsert, 200);
        
        foreach($dataInsertChunk as $item) {
            MarketCsgoempire::insert($item);
        }
        return 1;
    }

    public function csgoroll(Request $request){
        MarketCsgoroll::truncate();

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
            MarketCsgoroll::insert($item);
        }
        return 1;
    }

    public function csgorollDefault(Request $request){
        $data = $request->all();
        $dataStore      = json_decode($data['data_store'], true);

        $marketInventory = MarketInventory::all();
        foreach($marketInventory as $item){
            $name       = $item->name;
            foreach($dataStore as $storeItem) {
                if($item->name == $storeItem['name']) {
                    $item->csgoroll_default = $storeItem['price'];
                    $item->save();
                }
            }
        }
        return 1;
    }

    public function listInventory(){
        $marketInventory = MarketInventory::select(['name'])->get();
        return $marketInventory->toArray();
    }

    public function downloadData(Request $request){
        $data = $request->all();
        $rateCsgoempire         = $data['csgoempire'];
        $rateBuff               = $data['buff'];
        $rateCsgoroll           = $data['csgoroll'];

        $marketInventory = MarketInventory::all();

        foreach($marketInventory as $item){
            $name       = $item->name;
            $csgoroll   = MarketCsgoroll::where('name', $name)
                                        ->selectRaw('name, price, CAST(price as DECIMAL(9,2)) _price')
                                        ->orderBy('_price', 'ASC')->first();
            $csgoempire = MarketCsgoempire::where('name', $name)
                                        ->selectRaw('name, price, CAST(price as DECIMAL(9,2)) _price')
                                        ->orderBy('_price', 'ASC')->first();
            $buff       = MarketBuff::where('name', $name)
                                        ->selectRaw('name, price, CAST(price as DECIMAL(9,2)) _price')
                                        ->orderBy('_price', 'ASC')->first();
            $max        = 0;

            // csgoroll
            if($csgoroll) {
                if($csgoroll->price != 0) {
                    $priceCsgoroll = (double)$csgoroll->price  * (double)$rateCsgoroll;
                
                    if($priceCsgoroll > $max) {
                        $max = $priceCsgoroll;
                        $item->tick = 'csgoroll';
                    }
                    $item->csgoroll = $this->formatPrice($csgoroll->price, $priceCsgoroll);
                } else {
                    $item->csgoroll = '';
                }
            }
            if(!$item->csgoroll) {
                $priceCsgoroll = ((double)$item->csgoroll_default * 1.12)  * (double)$rateCsgoroll;
                if($priceCsgoroll > $max) {
                    $max = $priceCsgoroll;
                    $item->tick = 'csgoroll';
                }
                if((double)$item->csgoroll_default > 0) {
                    $item->csgoroll = $this->formatPrice($item->csgoroll_default, $priceCsgoroll, true);
                } else {
                    $item->csgoroll = '';
                }
            }

            // buff
            if($buff) {
                $priceBuff = (double)$buff->price * (double)$rateBuff;

                if($priceBuff > $max) {
                    $max = $priceBuff;
                    $item->tick = 'buff';
                }
                $item->buff = $this->formatPrice($buff->price, $priceBuff);
                $item->buff_sort = (double)$buff->price;
            }

            // csgoempire
            if($csgoempire) {
                $priceCsgoempire = (double)$csgoempire->price * (double)$rateCsgoempire;

                if($priceCsgoempire > $max) {
                    $max = $priceCsgoempire;
                    $item->tick = 'csgoempire';
                }
                $item->csgoempire = $this->formatPrice($csgoempire->price, $priceCsgoempire);
            } else {
                if((double)$item->csgoempire_default > 0) {
                    $priceCsgoempire = (double)$item->csgoempire_default * 1.06 * (double)$rateCsgoempire;

                    if($priceCsgoempire > $max) {
                        $max = $priceCsgoempire;
                        $item->tick = 'csgoempire';
                    }
                    $item->csgoempire = $this->formatPrice($item->csgoempire_default, $priceCsgoempire, true);
                } else {
                    $item->csgoempire = 'Not Accepted';
                }
            }
            $item->save();
        }

        $data = MarketInventory::select('name', 'csgoempire', 'buff', 'csgoroll', 'tick')
                            ->orderBy('buff_sort', 'DESC')
                            ->get()->toArray();
        return $data;
    }

    public function checkReady(){
        $marketInventoryCount = MarketInventory::count();
        $marketCsgorollCount = MarketCsgoroll::count();
        if($marketCsgorollCount > 0) {
            return 1;
        }
        return 0;
    }

    public function emptyTable(){
        MarketBuff::truncate();
        MarketCsgoempire::truncate();
        MarketCsgoroll::truncate();
        MarketInventory::truncate();

        return 1;
    }

    private function formatPrice($priceBefore, $priceAfter, $default = false){
        if($default) {
            return number_format((double)$priceBefore, 2) . ' (' . number_format($priceAfter, 2) . ') ' . '(default)';
        }
        return number_format((double)$priceBefore, 2) . ' (' . number_format($priceAfter, 2) . ')';
    }
}
