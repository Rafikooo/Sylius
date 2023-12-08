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

namespace Sylius\Bundle\CoreBundle\Provider;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Sales\Mapper\SalesPeriodMapperInterface;
use Sylius\Component\Core\Sales\Provider\SalesPerPeriodProviderInterface;
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;

final class SalesPerPeriodProvider extends AbstractHydrator implements SalesPerPeriodProviderInterface
{
    /** @param EntityRepository<OrderInterface> $orderRepository */
    public function __construct(private EntityRepository $orderRepository, private SalesPeriodMapperInterface $salesPeriodMapper)
    {
    }

    public function provide(SalesPeriod $salesPeriod, ChannelInterface $channel): array
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o')
            ->select('SUM(o.total) AS total')
            ->andWhere('o.paymentState = :state')
            ->andWhere('o.channel = :channel')
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->setParameter('channel', $channel);

        switch ($salesPeriod->getInterval()) {
            case 'year':
                $this->addYearModifier($queryBuilder, $salesPeriod);

                break;
            case 'month':
                $this->addMonthModifier($queryBuilder, $salesPeriod);

                break;
            case 'week':
                $this->addWeekModifier($queryBuilder, $salesPeriod);

                break;
            case 'day':
                $this->addDayModifier($queryBuilder, $salesPeriod);

                break;
            default:
                throw new \RuntimeException(sprintf('Interval "%s" not supported.', $salesPeriod->getInterval()));
        }

        $ordersTotals = $queryBuilder->getQuery()->getArrayResult();

        return $this->salesPeriodMapper->map($salesPeriod, $ordersTotals);
    }

    private function addYearModifier(QueryBuilder $queryBuilder, SalesPeriod $salesPeriod): void
    {
        $queryBuilder
            ->addSelect('YEAR(o.checkoutCompletedAt) as year')
            ->groupBy('year')
            ->andWhere('YEAR(o.checkoutCompletedAt) >= :startYear AND YEAR(o.checkoutCompletedAt) <= :endYear')
            ->setParameter('startYear', $salesPeriod->getStartDate()->format('Y'))
            ->setParameter('endYear', $salesPeriod->getEndDate()->format('Y'))
        ;
    }

    private function addMonthModifier(QueryBuilder $queryBuilder, SalesPeriod $salesPeriod): void
    {
        $queryBuilder
            ->addSelect('YEAR(o.checkoutCompletedAt) as year')
            ->addSelect('MONTH(o.checkoutCompletedAt) as month')
            ->groupBy('year')
            ->addGroupBy('month')
            ->andWhere($queryBuilder->expr()->orX(
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) >= :startMonth AND MONTH(o.checkoutCompletedAt) <= :endMonth',
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) != :endYear AND MONTH(o.checkoutCompletedAt) >= :startMonth',
                'YEAR(o.checkoutCompletedAt) = :endYear AND YEAR(o.checkoutCompletedAt) != :startYear AND MONTH(o.checkoutCompletedAt) <= :endMonth',
                'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
            ))
            ->setParameter('startYear', $salesPeriod->getStartDate()->format('Y'))
            ->setParameter('startMonth', $salesPeriod->getStartDate()->format('n'))
            ->setParameter('endYear', $salesPeriod->getEndDate()->format('Y'))
            ->setParameter('endMonth', $salesPeriod->getEndDate()->format('n'));
    }

    private function addWeekModifier(QueryBuilder $queryBuilder, SalesPeriod $salesPeriod): void
    {
        $queryBuilder
            ->addSelect('YEAR(o.checkoutCompletedAt) as year')
            ->addSelect('WEEK(o.checkoutCompletedAt) as week')
            ->groupBy('year')
            ->addGroupBy('week')
            ->andWhere($queryBuilder->expr()->orX(
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND WEEK(o.checkoutCompletedAt) >= :startWeek AND WEEK(o.checkoutCompletedAt) <= :endWeek',
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) != :endYear AND WEEK(o.checkoutCompletedAt) >= :startWeek',
                'YEAR(o.checkoutCompletedAt) = :endYear AND YEAR(o.checkoutCompletedAt) != :startYear AND WEEK(o.checkoutCompletedAt) <= :endWeek',
                'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
            ))
            ->setParameter('startYear', $salesPeriod->getStartDate()->format('Y'))
            ->setParameter('startWeek', (ltrim($salesPeriod->getStartDate()->format('W'), '0') ?: '0'))
            ->setParameter('endYear', $salesPeriod->getEndDate()->format('Y'))
            ->setParameter('endWeek', (ltrim($salesPeriod->getEndDate()->format('W'), '0') ?: '0'));
    }

    private function addDayModifier(QueryBuilder $queryBuilder, SalesPeriod $salesPeriod): void
    {
        $queryBuilder
            ->addSelect('YEAR(o.checkoutCompletedAt) as year')
            ->addSelect('MONTH(o.checkoutCompletedAt) as month')
            ->addSelect('DAY(o.checkoutCompletedAt) as day')
            ->groupBy('year')
            ->addGroupBy('month')
            ->addGroupBy('day')
            ->andWhere($queryBuilder->expr()->orX(
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) = :startMonth AND MONTH(o.checkoutCompletedAt) = :endMonth AND DAY(o.checkoutCompletedAt) >= :startDay AND DAY(o.checkoutCompletedAt) <= :endDay',
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) = :startMonth AND MONTH(o.checkoutCompletedAt) != :endMonth AND DAY(o.checkoutCompletedAt) >= :startDay',
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) = :endMonth AND MONTH(o.checkoutCompletedAt) != :startMonth AND DAY(o.checkoutCompletedAt) <= :endDay',
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) = :endYear AND MONTH(o.checkoutCompletedAt) > :startMonth AND MONTH(o.checkoutCompletedAt) < :endMonth',
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) != :endYear AND MONTH(o.checkoutCompletedAt) = :startMonth AND DAY(o.checkoutCompletedAt) >= :startDay',
                'YEAR(o.checkoutCompletedAt) = :startYear AND YEAR(o.checkoutCompletedAt) != :endYear AND MONTH(o.checkoutCompletedAt) > :startMonth',
                'YEAR(o.checkoutCompletedAt) = :endYear AND YEAR(o.checkoutCompletedAt) != :startYear AND MONTH(o.checkoutCompletedAt) = :endMonth AND DAY(o.checkoutCompletedAt) <= :endDay',
                'YEAR(o.checkoutCompletedAt) = :endYear AND YEAR(o.checkoutCompletedAt) != :startYear AND MONTH(o.checkoutCompletedAt) < :endMonth',
                'YEAR(o.checkoutCompletedAt) > :startYear AND YEAR(o.checkoutCompletedAt) < :endYear',
            ))
            ->setParameter('startYear', $salesPeriod->getStartDate()->format('Y'))
            ->setParameter('startMonth', $salesPeriod->getStartDate()->format('n'))
            ->setParameter('startDay', $salesPeriod->getStartDate()->format('j'))
            ->setParameter('endYear', $salesPeriod->getEndDate()->format('Y'))
            ->setParameter('endMonth', $salesPeriod->getEndDate()->format('n'))
            ->setParameter('endDay', $salesPeriod->getEndDate()->format('j'))
        ;
    }

    protected function hydrateAllData()
    {
    }
}
