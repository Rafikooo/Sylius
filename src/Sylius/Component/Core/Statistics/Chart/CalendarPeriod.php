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

class CalendarPeriod
{
    public const ANNUAL = 'annual';

    public const QUARTERLY = 'quarterly';

    public const MONTHLY = 'monthly';

    public const WEEKLY = 'weekly';

    public const DAILY = 'daily';

    private function __construct()
    {
    }
}
