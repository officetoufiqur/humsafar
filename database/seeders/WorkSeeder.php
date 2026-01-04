<?php

namespace Database\Seeders;

use App\Models\Work;
use Illuminate\Database\Seeder;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Work::insert([
            [
                'heading' => 'We put you in touch with nearby girls and guys!',
                'subheading' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tempus eleifend risus ut congue. Pellentesque nec lorem elit. Pellentesque convallis mauris nisl eu dapibus pharetra eu tristique rhoncus consequat.',
                'step' => '1,2,3 easy steps!',
                'title' => 'Find out everything you need to know and more about how our website works.',
                'subtitle' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tempus eleifend risus ut congue. Pellentesque nec lorem elit. Pellentesque convallis mauris nisl eu dapibus pharetra eu tristique rhoncus consequat. Vestibulum rhoncus porta sem molestudae magna mollis euismod consectetur dolor sit amet ultricies vehicula ut elit. Nullam quis risus eget urna mollis ornare.',
                'image' => '/assets/images/couple-coffee.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
