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

class CalendarIntervalType
{
    public const YEAR = 'year';

    public const QUARTER = 'quarter';

    public const MONTH = 'month';

    public const WEEK = 'week';

    public const DAY = 'day';

    /** @return array<string> */
    public static function getAllTypes(): array
    {
        $reflection = new \ReflectionClass(__CLASS__);

        return $reflection->getConstants();
    }

    private function __construct()
    {
    }
}
