<?php

namespace App\Tests\CoreLogic;

use \PHPUnit\Framework\TestCase;
use App\CoreLogic\Calculator;

/*
    На 05.05.2022 преподавател поставя задача на студент, да завърши определен видеокурс до 10.05.2022 най-късно.
    На 11.06.2022 е планиран изпит по дисциплината. Общото видеосъдържание е с обем 12 часа. От това задание могат да
    се пресметнат среднодневната продължителност на видео обучението, което студентът трябва да гледа, за да успее да
    мине през цялото видеосъдържание и да е подготвен за изпита.
    Във всеки един момент, студентът може да провери дали се движи с добра скорост, като подава текущият му прогрес по
    видео съдържанието, т.е. видео материала, който вече е изгледал като процент от всичкият видео материал.
    Комбинирайки данните, може да се каже бинарно дали прогреса до тук е достатъчен, за да завърши курса до крайната
    дата или трябва да се увеличи времето за гледане на ден, за да се постигне целта.
 */


class CalculatorTest extends TestCase
{
    public \DateTimeImmutable $currentDateTime;

    public function setUp():void {
        $this->currentDateTime = new \DateTimeImmutable();
        $this->currentDateTime->format(DATE_RFC3339);
    }

    public function testProgressIsComplete()
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
        $this->assertEquals(5, $result['needed_daily_learning_time']);
//        dd([$calculator, $result]);
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

//
//    public function testNowIsBeforeStartDate()
//    {
//
//    }

}