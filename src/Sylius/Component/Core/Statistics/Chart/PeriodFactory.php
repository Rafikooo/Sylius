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

namespace Sylius\Component\Core\Statistics\Chart;

use Sylius\Component\Core\DateTime\Period;

class PeriodFactory implements PeriodFactoryInterface
{
    public function createAnnualCalendarPeriod(int $year, string $calendarIntervalType): Period
    {
        if (!in_array($calendarIntervalType, CalendarIntervalType::getAllTypes(), true)) {
            throw new \InvalidArgumentException(
                sprintf('Calendar interval type "%s" does not exist.', $calendarIntervalType),
            );
        }

        $startDate = new \DateTimeImmutable::createFromFormat()
        $endDate = new \DateTimeImmutable(sprintf('%d-12-31 23:59:59', $year));

        $interval = \DateInterval::createFromDateString(sprintf('1 %s', $calendarIntervalType));

        return new Period($startDate, $endDate, $interval);
    }
}
