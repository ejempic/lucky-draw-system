<?php

namespace App\Http\Controllers;

use App\UserPrizes;
use App\Winners;
use App\WinningNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrawController extends Controller
{
    //
    public function draw(Request $request)
    {
        $exclude_ids = Winners::pluck('user_id');
        if($request->prize == 1){
            $winner_exist = Winners::where('prizes_id',$request->prize)->first();
            if($winner_exist){
                return ['status'=>'error','msg'=>'The Grand Prize have already been awarded!'];
            }


            $user_winn = WinningNumbers::whereNotIn('user_id',$exclude_ids)->select('user_id', DB::raw('count(*) as numbers'))
                ->groupBy('user_id')
                ->get();
            $highiest_number_quantity = $user_winn->max('numbers');

            $user_ids = collect($user_winn)->where('numbers',$highiest_number_quantity);
            $user_ids_array = [];
            foreach(array_values($user_ids->toArray()) as $array){
                $user_ids_array[] = $array['user_id'];
            }
            $get_numbers = WinningNumbers::whereIn('user_id',$user_ids_array)->get();

            $get_winner = $get_numbers->random(1)->first();

            Winners::create(['user_id'=>$get_winner->user_id, 'prizes_id'=>$request->prize,'numbers_id'=>$get_winner->id]);

            return ['status'=>'success','msg'=>"Lucky Winning Number: ".$get_winner['number']];

        }else{
            $user_prizes = UserPrizes::find($request->prize);
            $winner_counts = Winners::where('prizes_id',$request->prize)->count();
            if($user_prizes->limit==$winner_counts){
                return ['status'=>'error','msg'=>'The Prize have already reach its limit!'];
            }
            $get_numbers = WinningNumbers::whereNotIn('user_id',$exclude_ids)->get();
            if(count($get_numbers)==0){
                return ['status'=>'error','msg'=>'Please create more users for the draw!'];
            }
            $get_winner = $get_numbers->random(1)->first();


            Winners::create(['user_id'=>$get_winner->user_id, 'prizes_id'=>$request->prize,'numbers_id'=>$get_winner->id]);

            return ['status'=>'success','msg'=>"Lucky Winning Number: ".$get_winner['number']];


        }

        return $get_random;
    }

    public function save_number(Request $request)
    {
        $check_number_exists = WinningNumbers::where('number',$request->number)->exists();
        if($check_number_exists){
            return 0;
        }
        if($request->id == '0'){
            $winning_number = new WinningNumbers();
            if(trim($request->number)== ''){
                return 3;
            }
        }else{
            $winning_number = WinningNumbers::find($request->id);
            if(trim($request->number)== ''){
                $winning_number->delete();
                return 3;
            }
        }
        $winning_number->user_id = $request->user_id;
        $winning_number->number = $request->number;
        $winning_number->save();
        return 1;
    }
}
