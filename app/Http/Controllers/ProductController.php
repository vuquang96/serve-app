<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buff;
use App\Models\UserRequest;
use App\Models\Csgoroll;
use DB;
use Carbon\Carbon;

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
}
