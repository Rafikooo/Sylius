<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Controller;

use Sylius\Bundle\ApiBundle\Query\GetStatistics;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class GetStatisticsAction
{
    use HandleTrait;

    /** @var array<string> */
    private array $requiredParameters = [
        'channelCode' => 'string',
        'startDate' => 'dateTime',
        'dateInterval' => 'dateInterval',
        'endDate' => 'dateTime',
    ];

    public function __construct(
        MessageBusInterface $queryBus,
        private SerializerInterface $serializer,
    ) {
        $this->messageBus = $queryBus;
    }

    public function __invoke(Request $request): Response
    {
        $violations = $this->validateRequiredParameters($request);

        $parameters = $request->query->all();

        if (count($violations) > 0) {
            return new JsonResponse(
                data: $this->serializer->serialize($violations, 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true,
            );
        }

        $period = new \DatePeriod(
            new \DateTimeImmutable($parameters['startDate']),
            new \DateInterval($parameters['dateInterval']),
            new \DateTimeImmutable($parameters['endDate']),
        );

        try {
            $this->validateEndDateAgainstInterval($period);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(
                data: $this->serializer->serialize(['message' => $exception->getMessage()], 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true,
            );
        }

        $query = new GetStatistics($period, $parameters['channelCode']);

        return new JsonResponse(data: $this->serializer->serialize($this->handle($query), 'json'), json: true);
    }

    private function validateRequiredParameters(Request $request): array
    {
        $violations = [];

        foreach ($this->requiredParameters as $parameterName => $parameterType) {
            $parameter = $request->query->get($parameterName);

            if ($parameter === null) {
                $violations[] = [
                    'propertyPath' => $parameterName,
                    'message' => sprintf('Parameter "%s" is required.', $parameterName),
                ];

                continue;
            }

            if (empty($parameter)) {
                $violations[] = [
                    'propertyPath' => $parameterName,
                    'message' => sprintf('Parameter "%s" cannot be empty.', $parameterName),
                ];

                continue;
            }

            if ($parameterType === 'dateTime' && !$this->isISO8601DateTimeWithNoTimezone($parameter)) {
                $violations[] = [
                    'propertyPath' => $parameterName,
                    'message' => sprintf(
                        'Parameter "%s" must be a valid ISO8601 date time string without timezone.',
                        $parameterName,
                    ),
                ];
            } elseif ($parameterType === 'string' && !is_string($parameter)) {
                $violations[] = [
                    'propertyPath' => $parameterName,
                    'message' => sprintf('Parameter "%s" must be a string.', $parameterName),
                ];
            } elseif ($parameterType === 'dateInterval' && !$this->isValidInterval($parameter)) {
                $violations[] = [
                    'propertyPath' => $parameterName,
                    'message' => sprintf('Parameter "%s" must be a valid DateInterval string.', $parameterName),
                ];
            } elseif ($parameterType === 'int' && filter_var($parameter, \FILTER_VALIDATE_INT) === false) {
                $violations[] = [
                    'propertyPath' => $parameterName,
                    'message' => sprintf('Parameter "%s" must be an integer.', $parameterName),
                ];
            }
        }

        return $violations;
    }

    private function isISO8601DateTimeWithNoTimezone(?string $dateTime = null): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/', $dateTime) === 1;
    }

    private function isValidInterval(?string $interval = null): bool
    {
        try {
            new \DateInterval($interval);
        } catch (\Exception) {
            return false;
        }

        return true;
    }

    /**
     * Validates that the end date is a multiple of the interval.
     * The end date is adjusted by subtracting one second to make it inclusive (closed interval).
     * If the adjusted end date does not match the provided end date, an exception is thrown.
     */
    private function validateEndDateAgainstInterval(\DatePeriod $datePeriod): void
    {
        $currentDate = clone $datePeriod->getStartDate();
        $endDate = $datePeriod->getEndDate();
        $interval = $datePeriod->getDateInterval();

        while ($currentDate <= $endDate) {
            $currentDate = $currentDate->add($interval);
        }

        /** We shift to make closed interval. */
        $intervalEndDate = $currentDate->modify('-1 second');

        if ($intervalEndDate != $endDate) {
            throw new \InvalidArgumentException(sprintf(
                sprintf(
                    'End date "%s" must be multiple of interval, expected "%s"',
                    $endDate->format('Y-m-d H:i:s'),
                    $intervalEndDate->format('Y-m-d H:i:s'),
                ),
            ));
        }
    }
}
