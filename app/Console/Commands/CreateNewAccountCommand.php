<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;

class CreateNewAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create:account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command for creating new Account with initial balance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $accountNo = $this->ask('What is your account_no?');
        $userId = $this->ask('Which user owns this Account?');
        $type = $this->ask('What is the type of your Account?', 'normal');
        $amount = $this->ask('How much is your initial Account balance?');

        /** @var Account $account */
        $account = Account::query()->create([
            'user_id' => $userId,
            'type' => $type,
            'account_no' => $accountNo,
            'balance' => $amount,
        ]);

        $msg = sprintf(
            'A new Account with ID: %s, user_id: %s, type: %s, account_no: %s and balance: %s was created',
            $account->id,
            $account->user_id,
            $account->type,
            $account->account_no,
            $account->balance,
        );

        $this->info($msg);

        return Command::SUCCESS;
    }
}
