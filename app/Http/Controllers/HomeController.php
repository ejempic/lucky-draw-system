<?php

namespace App\Http\Controllers;

use App\User;
use App\UserPrizes;
use App\Winners;
use App\WinningNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function welcome()
    {
        $prizes = UserPrizes::get();


        return view('welcome',compact('prizes'));

    }

    public function index()
    {
        $prizes = UserPrizes::get();
        $winners = Winners::with('prize')->get();
        $highiest_number_quantity = WinningNumbers::select('user_id', DB::raw('count(*) as numbers'))
            ->groupBy('user_id')
            ->get()
            ->max('numbers');
        if($highiest_number_quantity < 2){
            $highiest_number_quantity = 2;
        }



        $users = User::role('member')->with('numbers','winners.prize')->get();


//        return $users;


        return view('home',compact('prizes','users','winners','highiest_number_quantity'));
    }

    public function generate_random_user()
    {
        $faker = \Faker\Factory::create();
        $fake_name = $faker->name;
        $email = \Illuminate\Support\Str::slug($fake_name, '.').'@'.$faker->freeEmailDomain;
        $member = User::create([
            'name' => $fake_name,
            'email' => $email,
            'password' => bcrypt('password'),
        ]);
        $member->assignRole('member');
        return $member;

    }

    public function truncate_winners()
    {
        DB::table('winners')->truncate();
    }
    public function ajax_winners()
    {
        $prizes = UserPrizes::get();

        return view('partials.winners',compact('prizes'));

    }
    public function ajax_members()
    {
        $users = User::role('member')->with('numbers','winners.prize')->get();
        $highiest_number_quantity = WinningNumbers::select('user_id', DB::raw('count(*) as numbers'))
            ->groupBy('user_id')
            ->get()
            ->max('numbers');
        if($highiest_number_quantity < 2){
            $highiest_number_quantity = 2;
        }
        return view('partials.members',compact('users','highiest_number_quantity'));

    }
}
