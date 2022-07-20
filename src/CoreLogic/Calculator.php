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
    private DateTime $currentTime;

    private static array $possibleStatuses = [
        'ON_TRACK'=>'on track',
        'NOT_ON_TRACK'=>'not on track', 
        'OVERDUE'=>'overdue'
    ];
    private int $passedCourseDays;
    private int $expectedProgress;
    private string $progressStatus = '';

    /**
     * @param int $courseDuration in seconds
     * @param int $currentProgress in percent between [0, 100]
     * @param DateTimeImmutable $courseStart RFC-3339
     * @param DateTimeImmutable $dueDate RFC-3339
     * Constructor expects sanitized and plausible input and calculates derived data from the input
     */
    public function __construct(private readonly int $courseDuration, private readonly int $currentProgress, private readonly DateTimeImmutable $courseStart, private readonly DateTimeImmutable $dueDate)
    {
        $this->percentRemaining = 100 - $this->currentProgress;
        $this->currentTime = new DateTime();
        $this->timeRemaining = round((int) $this->dueDate->diff($this->currentTime)->format("v"));
        $this->totalCourseDays =  round($this->dueDate->diff($this->courseStart)->format("%a"));
        $this->passedCourseDays = round($this->currentTime->diff($this->courseStart)->format("%a"));
        $this->remainingCourseDays = $this->totalCourseDays - $this->passedCourseDays;
        $this->dailyDesiredProgress = round(100 / $this->totalCourseDays);

        $this->expectedProgress =  ($this->currentTime >= $this->courseStart) ? round($this->passedCourseDays * $this->dailyDesiredProgress) : 0 ;

        $this->remainingCourseSeconds = round($this->courseDuration - $this->currentProgress * $this->courseDuration / 100) ;

        $this->progressStatus = $this->calculateProgressStatus();
    }
    
    public function calculateOutput(): array
    {
        if($this->progressStatus == self::$possibleStatuses['OVERDUE']) {
            return [
                'progress_status' => $this->progressStatus,
                'expected_progress' => $this->expectedProgress,
                'needed_daily_learning_time' => -1 // the value of impossible
            ];
        }
        else {
            return [
                'progress_status' => $this->progressStatus,
                'expected_progress' => $this->expectedProgress,
                'needed_daily_learning_time' => $this->calculateNeededDailyLearningTime()
            ];
        }
    }
    
    private function calculateProgressStatus(): string
    {
        if($this->currentTime >= $this->dueDate) {
            return self::$possibleStatuses['OVERDUE'];
        }
        else if($this->currentProgress >= $this->expectedProgress || $this->currentTime < $this->courseStart) {
            return self::$possibleStatuses['ON_TRACK'];
        }
        else return self::$possibleStatuses['NOT_ON_TRACK'];
    }
    
    private function calculateNeededDailyLearningTime(): int
    {
        if($this->currentProgress == 100) {
            return 0;
        }
        else {
            if($this->remainingCourseDays > 1) {
                return round($this->remainingCourseSeconds / $this->remainingCourseDays);
            }
            else {
                return $this->remainingCourseSeconds;
            }
        }
    }
}