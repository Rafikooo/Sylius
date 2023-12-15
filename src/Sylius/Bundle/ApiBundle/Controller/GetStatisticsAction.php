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
use Sylius\Component\Core\Statistics\Chart\CalendarIntervalType;
use Sylius\Component\Core\Statistics\Chart\PeriodFactoryInterface;
use Sylius\Component\Core\Statistics\Chart\PeriodType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class GetStatisticsAction
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
        private PeriodFactoryInterface $periodFactory,
        private SerializerInterface $serializer,
    ) {
        $this->messageBus = $queryBus;
    }

    public function __invoke(Request $request): Response
    {
        $channelCode = $request->query->get('channelCode');

        if ($channelCode === null) {
            return new JsonResponse(['error' => 'Missing channelCode parameter.'], Response::HTTP_BAD_REQUEST);
        }

        $periodType = $request->query->get('periodType');

        if ($periodType === null) {
            return new JsonResponse(['error' => 'Missing periodType parameter.'], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array($periodType, [PeriodType::CALENDAR, PeriodType::CUSTOM])) {
            return new JsonResponse([
                'error' => sprintf(
                    'Invalid periodType parameter. Allowed values are "%s" and "%s".',
                    PeriodType::CALENDAR,
                    PeriodType::CUSTOM
                )
                ], Response::HTTP_BAD_REQUEST);
        }

        if ($periodType === PeriodType::CUSTOM) {
            throw new \Exception('Not implemented yet.');
        }

        $year = $request->query->get('year');

        if ($year === null) {
            return new JsonResponse(['error' => 'Missing year parameter.'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_numeric($year)) {
            return new JsonResponse(['error' => 'Invalid year parameter, must be numeric.'], Response::HTTP_BAD_REQUEST);
        }

        $calendarIntervalType = $request->query->get('calendarIntervalType');

        if ($calendarIntervalType === null) {
            return new JsonResponse(['error' => 'Missing calendarIntervalType parameter.'], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array($calendarIntervalType, CalendarIntervalType::getAllTypes(), true)) {
            return new JsonResponse([
                'error' => sprintf(
                    'Invalid calendarIntervalType parameter. Allowed values are "%s".',
                    implode('", "', CalendarIntervalType::getAllTypes())
                )
            ], Response::HTTP_BAD_REQUEST);
        }

        $period = $this->periodFactory->createAnnualCalendarPeriod(
            year: (int)$request->query->get('year'),
            calendarIntervalType: $request->query->get('calendarIntervalType'),
        );


        //waliduj $period

        return new JsonResponse(
            data: $this->serializer->serialize($this->handle(new GetStatistics($period, $channelCode)), 'json'),
            json: true,
        );
    }
}
