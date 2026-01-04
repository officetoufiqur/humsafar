<?php

namespace Database\Seeders;

use App\Models\WorkStep;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkStep::insert([
            [
                'work_id' => 1,
                'title' => 'Create your free account',
                'subtitle' => 'Nulla vitae elit libero pharetra augue dapibus.',
            ],
            [
                'work_id' => 1,
                'title' => 'Create your details',
                'subtitle' => 'Vivamus sagittis lacus vel augue laoreet.',
            ],
            [
                'work_id' => 1,
                'title' => 'Connect with users',
                'subtitle' => 'Cras mattis consectetur purus sit amet.',
            ]
        ]);
    }
}
