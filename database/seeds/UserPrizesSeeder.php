<?php

use Illuminate\Database\Seeder;

class UserPrizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\UserPrizes::create(['name'=>'Grand Prize','limit'=>1]);
        \App\UserPrizes::create(['name'=>'Second Prize','limit'=>2]);
        \App\UserPrizes::create(['name'=>'Third Prize','limit'=>3]);
    }
}
