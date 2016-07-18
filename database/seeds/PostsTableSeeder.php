<?php

use Illuminate\Database\Seeder;
use App\Posts;
class PostsTableSeeder extends Seeder
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

        foreach(range(1,10) as $index)
        {
        	Posts::create([
        		'author_id' =>$faker->numberBetween($min=1, $max=10),
        		'title'=>$faker->sentence($nbWords = 3),
        		'body'=>$faker->paragraph($nbSentences = 2),
        		'active' =>$faker->numberBetween($min=1,$max=1)
        		]);
        }
    }
}
