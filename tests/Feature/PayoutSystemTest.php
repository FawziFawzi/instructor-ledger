<?php

use App\Enums\PayoutStatus;
use App\Jobs\ProcessInstructorPayoutJob;
use App\Models\InstructorBalance;
use App\Models\Payout;
use App\Models\User;
use App\Services\MockPaymentGatewayService;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Artisan;

uses(DatabaseTruncation::class);

function createInstructor()
{
    return User::factory()->create([
        'type' => 'instructor',
    ]);
}
function createInstructorBalance($instructor)
{
    return InstructorBalance::create([
        'instructor_id' => $instructor->id,
        'total_balance' => 10000,
        'pending_balance' => 10000,
        'available_balance' => 0,
    ]);
}

it('running payout process twice never double pays', function () {

    $instructor = createInstructor();

    createInstructorBalance($instructor);

    // Mock Gateway

    $gateway = Mockery::mock(
        MockPaymentGatewayService::class
    );

    $gateway->shouldReceive('transfer')
        ->once()
        ->andReturn('success');

    $this->app->instance(
        MockPaymentGatewayService::class,
        $gateway
    );

    // Run payout process twice
    Artisan::call('app:process-payouts');
    Artisan::call('app:process-payouts');

    expect(
        Payout::query()->count()
    )->toBe(1);

    $payout = Payout::query()->first();

    expect($payout->status)
        ->toBe(
            PayoutStatus::Success
        );

    $balance = InstructorBalance::query()
        ->where(
            'instructor_id',
            $instructor->id
        )
        ->first();

    expect(
        $balance->pending_balance
    )->toBe(0);
});


it('retried jobs never double pay', function () {

    $instructor = createInstructor();

    createInstructorBalance($instructor);

    $gateway = Mockery::mock(MockPaymentGatewayService::class);

    $gateway->shouldReceive('transfer')
        ->once()
        ->andReturn('success');

    $job = new ProcessInstructorPayoutJob($instructor->id);

    // Simulate multiple retries
    $job->handle($gateway);
    $job->handle($gateway);
    $job->handle($gateway);

    expect(Payout::count())->toBe(1);

    $balance = InstructorBalance::where('instructor_id', $instructor->id)->first();

    expect($balance->pending_balance)->toBe(0);
});


it('unreliable provider responses never cause duplicate payments', function () {

    $instructor = createInstructor();
    createInstructorBalance($instructor);

    $gateway = Mockery::mock(
        MockPaymentGatewayService::class
    );

    $gateway->shouldReceive('transfer')
        ->once()
        ->andThrow(
            new Exception('Gateway timeout')
        );

    $gateway->shouldReceive('verifyTransfer')
        ->once()
        ->andReturn('success');

    $this->app->instance(
        MockPaymentGatewayService::class,
        $gateway
    );

    Artisan::call('app:process-payouts');

    $payout = Payout::query()->first();

    expect($payout->status)
        ->toBe(
            PayoutStatus::PendingVerification
        );

    Artisan::call('app:reconcile-payouts');

    $payout->refresh();

    expect(
        Payout::query()->count()
    )->toBe(1);

    expect($payout->status)
        ->toBe(
            PayoutStatus::Success
        );

    $balance = InstructorBalance::query()
        ->where(
            'instructor_id',
            $instructor->id
        )
        ->first();

    expect(
        $balance->pending_balance
    )->toBe(0);
    expect(
        $balance->available_balance
    )->toBe(10000);
});
