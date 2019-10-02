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
        /** CHECKING IF THE PRIZE HAS REACH ITS LIMIT */
        $user_prizes = UserPrizes::find($request->prize);
        $winner_counts = Winners::where('prizes_id',$request->prize)->count();
        if($user_prizes->limit==$winner_counts){
            return ['status'=>'error','msg'=>'The Prize have already reached its limit!'];
        }

        /** GETTING THE USER ID OF THE WINNERS TO EXCLUDE IN QUERIES */
        $exclude_ids = Winners::pluck('user_id');

        if($request->random == "1"){

            /** Excluding the ids */
            $winning_numbers_exclude_ids = WinningNumbers::whereNotIn('user_id',$exclude_ids);

            if($request->prize == 1){
                /** Getting the highest count of winning numbers for the grand prize */
                $user_winn = $winning_numbers_exclude_ids
                    ->select('user_id', DB::raw('count(*) as numbers'))
                    ->groupBy('user_id')
                    ->get();
                $highest_number_quantity = $user_winn->max('numbers');
                $user_ids = collect($user_winn)->where('numbers',$highest_number_quantity);
                $user_ids_array = [];
                foreach(array_values($user_ids->toArray()) as $array){
                    $user_ids_array[] = $array['user_id'];
                }
                $get_numbers = WinningNumbers::whereIn('user_id',$user_ids_array)->get();
            }else{
                $get_numbers = $winning_numbers_exclude_ids->get();
            }

            /** Error checking if the number of winning numbers that are excluded in 0 */
            if(count($get_numbers)==0){
                return ['status'=>'error','msg'=>'Please create more users for the draw!'];
            }

            /** Performing the random draw */
            $get_winner = $get_numbers->random(1)->first();

        }else{

            /** Checking if the number input is null */

            if($request->number !=null){

                /** Checking if the number is already a winner  */
                $already_a_winning_number = WinningNumbers::where('number',$request->number)->first();
                $already_a_winner = Winners::where('numbers_id',$already_a_winning_number->id)->first();
                if($already_a_winner){
                    return ['status'=>'error','msg'=>'This number is already a winning number!'];
                }

                /** Getting the number from the excluded winners  */
                $get_winner = WinningNumbers::whereNotIn('user_id',$exclude_ids)->where('number',$request->number)->first();
                if(!$get_winner){
                    return ['status'=>'error','msg'=>'Can\'t find winning number, please try another number!'];
                }
            }else{
                return ['status'=>'error','msg'=>'Winning numbers can\'t be empty!'];
            }
        }

        /** Saving the winner  */
        if($get_winner){
            Winners::create(['user_id'=>$get_winner->user_id, 'prizes_id'=>$request->prize,'numbers_id'=>$get_winner->id]);
        }else{
            return ['status'=>'error','msg'=>'There was an error in creating the winner!'];
        }

        return ['status'=>'success','msg'=>"Lucky Winning Number: ".$get_winner['number']];


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
