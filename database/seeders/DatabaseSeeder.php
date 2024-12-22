<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default user
        $user = User::create([
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@acme.test',
            'password' => Hash::make('ziD9ZoUG'),
        ]);

        Voucher::factory(30)
            ->for($user)
            ->has(VoucherLine::factory()->count(5), 'lines')
            ->create();

        User::factory(100)
            ->has(
                Voucher::factory()
                    ->has(VoucherLine::factory()->count(3), 'lines')
                    ->count(50)
            )
            ->create();
    }
}
