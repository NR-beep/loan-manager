<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Loan;
use App\Models\Payment;
use App\Livewire\LoanManager;
use Mockery;

class LoanManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $loanManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loanManager = new LoanManager;
    }

    public function test_amortization_schedule_with_interest()
    {
        $loan = Loan::factory()->make([
            'loan_amount' => 1000,
            'interest_rate' => 12, // 12% annual
            'loan_term' => 12, // 12 months
            'start_date' => '2024-01-01',
        ]);

        Mockery::mock('alias:App\Models\Payment')
            ->shouldReceive('create')
            ->times(12)
            ->andReturnNull();

        $loanManager = new LoanManager();
        $schedule = $loanManager->generatePayments($loan);

        $this->assertCount(12, $schedule);

        $expectedMonthlyPayment = 1000 * (0.01 * pow(1 + 0.01, 12)) / (pow(1 + 0.01, 12) - 1);
        $this->assertEquals(round($expectedMonthlyPayment, 2), $schedule[0]['total']);

        $this->assertEquals(round($schedule[0]['principal'] + $schedule[0]['interest'], 2), $schedule[0]['total']);

        $expectedBalanceAfterFirst = 1000 - $schedule[0]['principal'];
        $this->assertEquals(round($expectedBalanceAfterFirst, 2), $schedule[0]['balance']);

        $this->assertEquals('2024-02-01', $schedule[0]['date']);
        $this->assertEquals('2025-01-01', $schedule[11]['date']);

        $this->assertEquals(0, $schedule[11]['balance']);
    }

    public function test_amortization_schedule_zero_interest()
    {
        $loan = Loan::factory()->make([
            'loan_amount' => 1200,
            'interest_rate' => 0,
            'loan_term' => 12,
            'start_date' => '2024-01-01',
        ]);

        Mockery::mock('alias:App\Models\Payment')
            ->shouldReceive('create')
            ->times(12)
            ->andReturnNull();

        $schedule = $this->loanManager->generatePayments($loan);

        $this->assertCount(12, $schedule);

        $expectedMonthlyPayment = 1200 / 12;
        foreach ($schedule as $payment) {
            $this->assertEquals(round($expectedMonthlyPayment, 2), $payment['total']);
            $this->assertEquals(0, $payment['interest']);
        }

        $this->assertEquals(0, $schedule[11]['balance']);
    }

    public function test_one_month_loan()
    {
        $loan = new Loan([
            'id' => 1,
            'loan_amount' => 1000,
            'interest_rate' => 10,
            'loan_term' => 1,
            'start_date' => '2024-01-01',
        ]);

        Mockery::mock('alias:App\Models\Payment')
            ->shouldReceive('create')
            ->once()
            ->andReturnNull();

        $schedule = (new LoanManager())->generatePayments($loan);

        $this->assertCount(1, $schedule);
        $this->assertEquals(1000 + round(1000 * (10/100) / 12, 2), $schedule[0]['total']);
    }

    public function test_high_interest_loan()
    {
        $loan = new Loan([
            'id' => 1,
            'loan_amount' => 1000,
            'interest_rate' => 1200, // 100% per month
            'loan_term' => 3,
            'start_date' => '2024-01-01',
        ]);

        Mockery::mock('alias:App\Models\Payment')
            ->shouldReceive('create')
            ->times(3)
            ->andReturnNull();

        $schedule = (new LoanManager())->generatePayments($loan);

        $this->assertCount(3, $schedule);
        $this->assertTrue($schedule[0]['interest'] > $schedule[0]['principal']);
    }

    public function test_zero_interest()
    {
        $loan = new Loan([
            'id' => 1,
            'loan_amount' => 1200,
            'interest_rate' => 0,
            'loan_term' => 12,
            'start_date' => '2024-01-01',
        ]);

        Mockery::mock('alias:App\Models\Payment')
            ->shouldReceive('create')
            ->times(12)
            ->andReturnNull();

        $schedule = (new LoanManager())->generatePayments($loan);

        $this->assertCount(12, $schedule);
        foreach ($schedule as $payment) {
            $this->assertEquals(0, $payment['interest']);
            $this->assertEquals(100.00, $payment['principal']);
        }
    }

    public function test_zero_amount()
    {
        $loan = new Loan([
            'id' => 1,
            'loan_amount' => 0,
            'interest_rate' => 10,
            'loan_term' => 12,
            'start_date' => '2024-01-01',
        ]);

        Mockery::mock('alias:App\Models\Payment')
            ->shouldReceive('create')
            ->times(12)
            ->andReturnNull();

        $schedule = (new LoanManager())->generatePayments($loan);

        $this->assertCount(12, $schedule);
        foreach ($schedule as $payment) {
            $this->assertEquals(0.00, $payment['principal']);
            $this->assertEquals(0.00, $payment['interest']);
            $this->assertEquals(0.00, $payment['total']);
            $this->assertEquals(0.00, $payment['balance']);
        }
    }

    public function test_rounding_precision()
    {
        $loan = new Loan([
            'id' => 1,
            'loan_amount' => 1000,
            'interest_rate' => 5,
            'loan_term' => 3,
            'start_date' => '2024-01-01',
        ]);

        Mockery::mock('alias:App\Models\Payment')
            ->shouldReceive('create')
            ->times(3)
            ->andReturnNull();

        $schedule = (new LoanManager())->generatePayments($loan);

        $this->assertCount(3, $schedule);
        $this->assertEquals(0.00, round($schedule[2]['balance'], 2));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
