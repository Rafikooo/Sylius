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

namespace Sylius\Component\Core\DateTime;

class Period implements \IteratorAggregate
{
    private const YEAR = 'year';

    private const MONTH = 'month';

    private const WEEK = 'week';

    private const DAY = 'day';

    private \DatePeriod $period;

    public function __construct(
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        \DateInterval $interval,
    ) {
        if ($startDate >= $endDate) {
            throw new \InvalidArgumentException('Start date cannot be greater or equal to end date.');
        }

        $this->period = new \DatePeriod($startDate, $interval, $endDate);

        $this->period->getDateInterval()->format('Y-m-d H:i:s');
    }

    public function getIterator(): \DatePeriod
    {
        return $this->period;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->period->getStartDate()->format('Y-m-d H:i:s'));
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return  new \DateTimeImmutable($this->period->getEndDate()->format('Y-m-d H:i:s'));
    }

    public function getIntervalType(): string
    {
        $interval = $this->period->getDateInterval();

        if ($interval->y > 0) {
            return self::YEAR;
        }

        if ($interval->m > 0) {
            return self::MONTH;
        }

        if ($interval->d >= 7) {
            return self::WEEK;
        }

        return self::DAY;
    }
}
