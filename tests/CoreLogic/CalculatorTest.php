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

    public function testProgressIsComplete()
    {
        $duration = 123456;
        $progress = 100;
        $startDate = new \DateTimeImmutable('2022-07-15T15:52:01+00:00');
        $dueDate = new \DateTimeImmutable('2022-07-19T15:52:01+00:00');

        $calculator = new Calculator($duration, $progress, $startDate, $dueDate);
        $result = $calculator->calculateOutput();
        $this->assertEquals(0, $result['needed_daily_learning_time']);
    }


    public function testCourseInProgressComplete()
    {
        $duration = 123456;
        $progress = 14;
        $startDate = new \DateTimeImmutable('2022-07-15T15:52:01+00:00');
        $dueDate = new \DateTimeImmutable('2022-07-19T15:52:01+00:00');

        $calculator = new Calculator($duration, $progress, $startDate, $dueDate);
        $result = $calculator->calculateOutput();
        $this->assertEquals(2, $result['needed_daily_learning_time']);
        dd([$calculator, $result]);
        dd($result);
    }

//    public function testDueDateIsBeforeStartDate()
//    {
//
//    }
//
//    public function testDueDateIsBeforeNow()
//    {
//
//    }
//
//    public function testNowIsBeforeStartDate()
//    {
//
//    }

}