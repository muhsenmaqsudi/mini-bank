<?php

use App\DataObjects\SmsSendResDTO;
use App\Models\Card;
use App\Models\Transaction;
use App\Notifications\Supplier;
use App\ValueObjects\TransferType;
use Mockery\MockInterface;

it('returns a successful response', function () {
    $response = $this->getJson(route('up'));

    $response->assertStatus(200);
    $response->assertJson(['message' => 'MiniBank api is up & running']);
});

it('is required to provide required params for doing a account transfer', function () {
    $response = $this->postJson(route('account.transfer'));

    $response->assertJsonValidationErrors(['sender_card', 'receiving_card', 'amount']);
});

it('is only accepting a 16 number digits for card number', function () {
    $badData = [
        'sender_card' => '1234',
        'receiving_card' => '4321',
        'amount' => 10000,
    ];
    $response = $this->postJson(route('account.transfer'), $badData);

    $response->assertJsonValidationErrors(['sender_card', 'receiving_card']);
    $this->assertEquals($response->json('errors.sender_card')[0],'The sender card field must be 16 digits.');
    $this->assertEquals($response->json('errors.receiving_card')[0],'The receiving card field must be 16 digits.');
});

it('is only accepting a amount between 10000 and 500000000', function () {
    $badData = [
        'sender_card' => '6219861055567746',
        'receiving_card' => '6219861908496622',
        'amount' => '5000000000'
    ];
    $response = $this->postJson(route('account.transfer'), $badData);

    $response->assertJsonValidationErrors(['amount']);
    $this->assertEquals($response->json('errors.amount')[0],'The amount field must be between 10000 and 500000000.');
});

it('is only accepting a valid format for card number', function () {
    $badData = [
        'sender_card' => '6219861055567722',
        'receiving_card' => '6219861908496644',
        'amount' => 500000,
    ];
    $response = $this->postJson(route('account.transfer'), $badData);

    $response->assertJsonValidationErrors(['sender_card', 'receiving_card']);
    $this->assertEquals($response->json('errors.sender_card')[0],'The sender card format is invalid');
    $this->assertEquals($response->json('errors.receiving_card')[0],'The receiving card format is invalid');
});

it('is converting persian & arabic numbers to english numbers before doing a account transfer', function () {
    $this->mock(
        Supplier::class,
        function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->andReturn(new SmsSendResDTO(200, 'the mock is working'))
                ->twice();
        }
    );

    $this->seed();

    $badData = [
        'sender_card' => '۶۲۱۹۸۶۱۹۰۸۴۹۶۶۲۲', // persian
        'receiving_card' => '٦٢١٩٨٦١٠٥٥٥٦٧٧٤٦', // arabic
        'amount' => '۱۰۰۰۰'
    ];

    $this->postJson(
        route('account.transfer'),
        $badData,
    );

    $this->assertDatabaseHas('cards', ['card_no' => '6219861055567746']);
    $this->assertDatabaseMissing('cards', ['card_no' => '۶۲١۹۸۶۱۰۵۵۵۶۷۷۴۶']);
    $this->assertDatabaseMissing('cards', ['card_no' => '٦٢١٩٨٦١٠٥٥٥٦٧٧٤٦']);
});

it('is is doing a successful account transfer', function () {
    $this->mock(
        Supplier::class,
        function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->andReturn(new SmsSendResDTO(200, 'the mock is working'))
                ->twice();
        }
    );

    $this->seed();

    $correctData = [
        'sender_card' => '6219861055567746',
        'receiving_card' => '6219861908496622',
        'amount' => 10000,
    ];

    $response = $this->postJson(
        route('account.transfer'),
        $correctData,
    );

    $response->assertOk();
    $response->assertJson([
        'status' => true,
        'message' => 'The transfer is successful',
    ]);

    $thisTxn = Transaction::query()
        ->where('track_id', $trackId = $response->json('track_id'))
        ->first();

    $this->assertDatabaseHas('transactions', ['track_id' => $trackId]);
    $this->assertDatabaseHas('transfers', [
        'type' => TransferType::WITHDRAW->value,
        'txn_id' => $thisTxn->id,
    ]);
    $this->assertDatabaseHas('transfers', [
        'type' => TransferType::DEPOSIT->value,
        'txn_id' => $thisTxn->id,
    ]);
    $this->assertDatabaseHas('wages', ['txn_id' => $thisTxn->id]);
});

it('is is correctly updating account balance after a successful account transfer', function () {
    $this->mock(
        Supplier::class,
        function (MockInterface $mock) {
            $mock->shouldReceive('send')
                ->andReturn(new SmsSendResDTO(200, 'the mock is working'))
                ->twice();
        }
    );

    $this->seed();

    $correctData = [
        'sender_card' => '6219861055567746',
        'receiving_card' => '6219861908496622',
        'amount' => 10000,
    ];

    /** @var Card $senderCard */
    $senderCard = Card::query()->where('card_no', $correctData['sender_card'])->first();
    $senderCardBalance = $senderCard->account->balance;
    /** @var Card $receivingCard */
    $receivingCard = Card::query()->where('card_no', $correctData['receiving_card'])->first();
    $receivingCardBalance = $receivingCard->account->balance;

    $response = $this->postJson(
        route('account.transfer'),
        $correctData,
    );

    $response->assertOk();

    $transferWage = 5000;

    $this->assertEquals(
        $senderCardBalance - ($correctData['amount'] + $transferWage),
        $senderCard->fresh()->account->balance
    );

    expect($receivingCardBalance + $correctData['amount'])
        ->toBe($receivingCard->fresh()->account->balance);
});
