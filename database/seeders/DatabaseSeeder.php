<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\ValueObjects\AccountType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $user = \App\Models\User::factory()->create([
             'name' => 'Mohsen Maqsudi',
             'mobile' => '09354922919',
             'email' => 'mohsenmaqsudi@gmail.com',
         ]);

         $account = \App\Models\Account::factory()->create([
             'user_id' => $user->id,
             'type' => AccountType::NORMAL->value,
             'account_no' => '8302024773611',
             'balance' => "100000000",
         ]);

         \App\Models\Card::query()->create([
             'account_id' => $account->id,
             'card_no' => '6219861908496622',
         ]);

        $secondAccount = \App\Models\Account::factory()->create([
            'user_id' => $user->id,
            'type' => AccountType::NORMAL->value,
            'account_no' => '83080024773611',
            'balance' => "100000000",
        ]);

        \App\Models\Card::query()->create([
            'account_id' => $secondAccount->id,
            'card_no' => '6219861055567746',
        ]);
    }
}
