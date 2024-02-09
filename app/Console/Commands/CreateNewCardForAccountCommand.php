<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Card;
use Illuminate\Console\Command;

class CreateNewCardForAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create:card';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command for creating new Card for an Account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cardNo = $this->ask('What is your card_no?');
        $accountId = $this->ask('What is the id of your Account?');

        /** @var Card $card */
        $card = Card::query()->create([
            'account_id' => $accountId,
            'card_no' => $cardNo,
        ]);

        $msg = sprintf(
            'A new Card with ID: %s, card_no: %s for account with ID: %s was created',
            $card->id,
            $card->card_no,
            $card->account_id,
        );

        $this->info($msg);

        return Command::SUCCESS;
    }
}
