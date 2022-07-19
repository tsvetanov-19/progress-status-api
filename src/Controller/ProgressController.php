<?php

namespace App\Controller;

use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use DateTime;
use DateTimeImmutable;

use App\CoreLogic\Calculator;

class ProgressController extends AbstractController
{
    /**
     * @Route(
     *     "/statuses/{duration}/{currentProgress}/{dateCreated}/{dueDate}",
     *     name="current_status",
     *     methods={"GET"}
     *     )
     * @param Request $request
     * @param $duration
     * @param $currentProgress
     * @param $dateCreated
     * @param $dueDate
     * @return JsonResponse
     */
    public function status(Request $request, $duration, $currentProgress, $dateCreated, $dueDate): JsonResponse
    {
        if(!is_numeric($duration)) {
            $errors[] = "Duration must be an integer!";
        }

        if($duration < 0) {
            $errors[] = "Duration must be positive!";
        }

        if(!is_numeric($currentProgress) || $currentProgress < 0 || $currentProgress > 100) {
            $errors[] = "Progress in % must be an integer between 0 and 100!";
        }

        $dateCreated = DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339, $dateCreated);
        if ($dateCreated=== false) {
            $errors[] = "Date of creation in wrong format, must use RFC3339!";
        }

        $dueDate = DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339, $dueDate) ;
        if ($dueDate === false) {
            $errors[] = "Due date in wrong format, must use RFC3339!";
        }

        if(!empty($errors)) {
            $status = Response::HTTP_BAD_REQUEST;
            $message = ['errorMessage' => implode(PHP_EOL, $errors)];
        }

        else {
            $status = Response::HTTP_OK;
            $progress_status = '';
            $expected_progress = 0;
            $needed_daily_learning_time = 0;
            $put = [
                'progress_status' => $progress_status,
                'expected_progress' => $expected_progress,
                'needed_daily_learning_time' => $needed_daily_learning_time,
                'input' => [$duration, $currentProgress, $dateCreated, $dueDate]
            ];
            $message = $put;
//            $calculator = new Calculator($duration, $currentProgress, $dateCreated, $dueDate);
//            $message = $calculator->calculateOutput();

        }

        return $this->json($message, $status);
    }
}