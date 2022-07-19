<?php

namespace App\CoreLogic;
use DateTime;
use DateTimeImmutable;

class Calculator {

    private int $percentRemaining;
    private int $timeRemaining;
    private int $totalCourseDays;
    private int $remainingCourseDays;
    private int $remainingCourseSeconds;
    private float $dailyDesiredProgress;
    private \DateTime $currentTime;

    private static array $possibleStatuses = [
        'ON_TRACK'=>'on track',
        'NOT_ON_TRACK'=>'not on track', 
        'OVERDUE'=>'overdue'
    ];
    private int $passedCourseDays;
    private int $expectedProgress;

    /**
     * @param int $courseDuration
     * @param int $currentProgress
     * @param DateTimeImmutable $courseStart
     * @param DateTimeImmutable $dueDate
     * Constructor expects sanitized and plausible input and calculates derived data from the input
     */
    public function __construct(private readonly int $courseDuration, private readonly int $currentProgress, private readonly DateTimeImmutable $courseStart, private readonly DateTimeImmutable $dueDate)
    {
        $this->percentRemaining = 100 - $this->currentProgress;
        $this->currentTime = new DateTime();
        $this->timeRemaining = (int) $this->dueDate->diff($this->currentTime)->format("v");
        $this->totalCourseDays =  $this->dueDate->diff($this->courseStart)->format("%a");
        $this->passedCourseDays = (int) $this->currentTime->diff($this->courseStart)->format("%a");
        $this->remainingCourseDays = $this->totalCourseDays - $this->passedCourseDays;
        $this->dailyDesiredProgress = 100 / $this->totalCourseDays;

        $this->expectedProgress = $this->passedCourseDays * $this->dailyDesiredProgress;

        $this->remainingCourseSeconds = $this->currentProgress * $this->courseDuration / 100;
    }
    
    public function calculateOutput(): array
    {
        return [
            'progress_status' => $this->calculateProgressStatus(),
            'expected_progress' => $this->expectedProgress,
            'needed_daily_learning_time' => $this->calculateNeededDailyLearningTime()
        ];
    }
    
    private function calculateProgressStatus(): string
    {
        if($this->currentTime >= $this->dueDate) {
            return self::$possibleStatuses['OVERDUE'];
        }
        else if($this->currentProgress >= $this->expectedProgress) {
            return self::$possibleStatuses['ON_TRACK'];
        }
        else return self::$possibleStatuses['NOT_ON_TRACK'];
    }
    
//    private function calculateExpectedProgress(): int
//    {
//        return (int) $this->passedCourseDays * $this->dailyDesiredProgress;
//    }
    
    private function calculateNeededDailyLearningTime(): int
    {
        if($this->currentProgress == 100) {
            return 0;
        }
        else {
            return $this->remainingCourseSeconds / ($this->remainingCourseDays)  >0 ? $this->remainingCourseDays : 1;
        }
    }
}