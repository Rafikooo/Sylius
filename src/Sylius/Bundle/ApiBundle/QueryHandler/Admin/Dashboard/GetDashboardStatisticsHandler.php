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

namespace Sylius\Bundle\ApiBundle\QueryHandler\Admin\Dashboard;

use Sylius\Bundle\AdminBundle\Provider\StatisticsDataProvider;
use Sylius\Bundle\ApiBundle\Query\Admin\Dashboard\GetDashboardStatistics;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class GetDashboardStatisticsHandler
{
    /** @param ChannelRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(
        private StatisticsDataProvider $statisticsDataProvider,
        private ChannelRepositoryInterface $channelRepository,
    ) {
    }

    /** @return array<string, array<string, mixed>> */
    public function __invoke(GetDashboardStatistics $query): array
    {
        $startDate = new \DateTimeImmutable('first day of january this year');
        $endDate = new \DateTimeImmutable('first day of january next year');

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($query->getChannelCode());

        if ($channel === null) {
            throw new \InvalidArgumentException(sprintf('Channel with code "%s" does not exist.', $query->getChannelCode()));
        }

        return $this->statisticsDataProvider->getRawData(
            $channel,
            $startDate,
            $endDate,
            'month',
            false,
        );
    }
}
