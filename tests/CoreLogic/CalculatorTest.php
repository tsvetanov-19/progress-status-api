<?php

namespace App\Tests\CoreLogic;

use \PHPUnit\Framework\TestCase;
use App\CoreLogic\Calculator;

class CalculatorTest extends TestCase
{
    public \DateTimeImmutable $currentDateTime;

    public function setUp():void {
        $this->currentDateTime = new \DateTimeImmutable();
        $this->currentDateTime->format(DATE_RFC3339);
    }

    public function testCourseIsCompleted()
    {
        $duration = 123456;
        $progress = 100;
        $fiveDays = \DateInterval::createFromDateString('5 days');
        $startDate = $this->currentDateTime->sub($fiveDays);
        $dueDate = $this->currentDateTime->add($fiveDays);

        $calculator = new Calculator($duration, $progress, $startDate, $dueDate);
        $result = $calculator->calculateOutput();
        $this->assertEquals(0, $result['needed_daily_learning_time']);
    }


    public function testCourseInProgress()
    {
        $duration = 123456;
        $progress = 14;
        $fiveDays = \DateInterval::createFromDateString('5 days');
        $startDate = $this->currentDateTime->sub($fiveDays);
        $dueDate = $this->currentDateTime->add($fiveDays);


        $calculator = new Calculator($duration, $progress, $startDate, $dueDate);
        $result = $calculator->calculateOutput();
        $this->assertEquals(21234, $result['needed_daily_learning_time']);
    }

    public function testDueDateHasPassed()
    {
        $duration = 123456;
        $progress = 14;
        $fiveDays = \DateInterval::createFromDateString('5 days');
        $twoDays = \DateInterval::createFromDateString('2 days');
        $startDate = $this->currentDateTime->sub($fiveDays);
        $dueDate = $this->currentDateTime->sub($twoDays);

        $calculator = new Calculator($duration, $progress, $startDate, $dueDate);
        $result = $calculator->calculateOutput();
        $this->assertEquals('overdue', $result['progress_status']);
    }

    public function testCourseNotStartedYet()
    {
        $duration = 36000;
        $progress = 0;
        $twoDays = \DateInterval::createFromDateString('2 days');
        $tenDays = \DateInterval::createFromDateString('10 days');
        $startDate = $this->currentDateTime->add($twoDays);
        $dueDate = $this->currentDateTime->add($tenDays);


        $calculator = new Calculator($duration, $progress, $startDate, $dueDate);
        $result = $calculator->calculateOutput();
        $this->assertEquals('on track', $result['progress_status']);
        $this->assertEquals(0, $result['expected_progress']);

        $this->assertEquals(round($duration / 7), $result['needed_daily_learning_time']);
    }

    public function testProgressNotOnTrack()
    {
        $duration = 36000;
        $progress = 10;
        $twoDays = \DateInterval::createFromDateString('2 days');
        $eightDays = \DateInterval::createFromDateString('8 days');
        $startDate = $this->currentDateTime->sub($twoDays);
        $dueDate = $this->currentDateTime->add($eightDays);

        $calculator = new Calculator($duration, $progress, $startDate, $dueDate);
        $result = $calculator->calculateOutput();
        $this->assertEquals('not on track', $result['progress_status']);
        $this->assertEquals(20, $result['expected_progress']);

        $this->assertEquals($duration * (1-$progress/100) / 8, $result['needed_daily_learning_time']);
    }

}
