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

namespace spec\Sylius\Component\Core\Sales\Mapper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Sales\Mapper\SalesPeriodMapperInterface;
use Sylius\Component\Core\Sales\ValueObject\SalesInPeriod;
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;

final class SalesPeriodMapperSpec extends ObjectBehavior
{
    function it_implements_sales_sales_period_mapper_interface(): void
    {
        $this->shouldImplement(SalesPeriodMapperInterface::class);
    }

    function it_maps_orders_totals_to_sales_in_period_objects(
        SalesPeriod $salesPeriod,
        SalesInPeriod $lastYearSales,
        SalesInPeriod $thisYearSales,
    ): void {
        $ordersTotal = ['year' => 2020, 'month' => 1, 'total' => 1000];

        $startDate = new \DateTimeImmutable('first day of january this year 00:00:00');
        $endDate = new \DateTimeImmutable('last day of december this year 23:59:59');

        $salesPeriod->getStartDate()->willReturn($startDate);
        $salesPeriod->getEndDate()->willReturn($endDate);
        $salesPeriod->getInterval()->willReturn('year');

        $salesData = [$lastYearSales->getWrappedObject(), $thisYearSales->getWrappedObject()];

        $this->map($salesPeriod, $salesData)->shouldReturn($salesData);
    }
}
