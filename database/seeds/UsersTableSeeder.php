<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Posts;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker\Factory::create();

        foreach(range(1,2) as $index)
        {
        	User::create([
        		'name'=>$faker->firstName,
        		'email'=>$faker->email,
        		'password' => bcrypt('secret'),
        		'role'=>'admin'
        		]);
        }
    }
}
