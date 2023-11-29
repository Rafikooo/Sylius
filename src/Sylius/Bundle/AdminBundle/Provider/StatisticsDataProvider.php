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

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface;
use Sylius\Component\Core\Dashboard\Interval;
use Sylius\Component\Core\Dashboard\SalesDataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;

class StatisticsDataProvider implements StatisticsDataProviderInterface
{
    public function __construct(
        private DashboardStatisticsProviderInterface $statisticsProvider,
        private SalesDataProviderInterface $salesDataProvider,
        private MoneyFormatterInterface $moneyFormatter,
    ) {
    }

    /** @return array<string, array<string, mixed>> */
    public function getRawData(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $interval,
        bool|null $isCurrencyFormatted = true,
    ): array {
        if ($isCurrencyFormatted === null) {
            trigger_deprecation(
                'sylius/sylius',
                '1.13',
                'Not passing the $isCurrencyFormatted argument to %s() is deprecated and will not be prohibited in Sylius 2.0. Please pass it explicitly.',
                __METHOD__,
            );
        }

        $statistics = $this->statisticsProvider->getStatisticsForChannelInPeriod($channel, $startDate, $endDate);

        $salesSummary = $this->salesDataProvider->getSalesSummary(
            $channel,
            $startDate,
            $endDate,
            Interval::{$interval}(),
        );

        /** @var string $currencyCode */
        $currencyCode = $channel->getBaseCurrency()->getCode();

        $totalSales = $isCurrencyFormatted ? $this->moneyFormatter->format($statistics->getTotalSales(), $currencyCode) : $statistics->getTotalSales();
        $averageOrderValue = $isCurrencyFormatted ? $this->moneyFormatter->format($statistics->getAverageOrderValue(), $currencyCode) : $statistics->getAverageOrderValue();

        return [
            'sales_summary' => [
                'intervals' => $salesSummary->getIntervals(),
                'sales' => $salesSummary->getSales(),
            ],
            'channel' => [
                'base_currency_code' => $currencyCode,
                'channel_code' => $channel->getCode(),
            ],
            'statistics' => [
                'total_sales' => $totalSales,
                'number_of_new_orders' => $statistics->getNumberOfNewOrders(),
                'number_of_new_customers' => $statistics->getNumberOfNewCustomers(),
                'average_order_value' => $averageOrderValue,
            ],
        ];
    }
}
