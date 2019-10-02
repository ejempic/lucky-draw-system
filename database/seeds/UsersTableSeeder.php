<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\User;
use \Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->create_roles();

        $admin = User::create([
            'name' => "Admin",
            'email' => "admin@gmail.com",
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $faker = Faker\Factory::create();
        foreach(range(1,4) as $index){
            $fake_name = $faker->name;
            $email = \Illuminate\Support\Str::slug($fake_name, '.').'@'.$faker->freeEmailDomain;
            $member = User::create([
                'name' => $fake_name,
                'email' => $email,
                'password' => bcrypt('password'),
            ]);
            $member->assignRole('member');
        }


    }

    function create_roles(){
        Role::create(['name' => 'admin','guard_name'=>'web']);
        Role::create(['name' => 'member','guard_name'=>'web']);
    }
}
