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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class DashboardContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private DateTimeProviderInterface $dateTimeProvider,
    ) {
    }

    /**
     * @When I view statistics
     */
    public function iBrowseStatistics(): void
    {
        $this->client->index(Resources::STATISTICS);
    }

    /**
     * @When /^I view statistics for ("[^"]+" channel) and current year split by month$/
     * @When I choose :channel channel
     * @When I view statistics for :channel channel
     */
    public function iViewStatisticsForChannelAndYear(ChannelInterface $channel): void
    {
        $this->client->index(
            Resources::STATISTICS,
            [
                'channelCode' => $channel->getCode(),
                'startDate' => $this->dateTimeProvider->now()->format('Y-01-01\T00:00:00'),
                'dateInterval' => 'P1M',
                'endDate' => $this->dateTimeProvider->now()->format('Y-12-31\T23:59:59'),
            ],
        );
    }

    /**
     * @Then I should see :count new orders
     */
    public function iShouldSeeNewOrders(int $count): void
    {
        Assert::true(
            $this->responseChecker->hasValuesInSubresourceObject(
                $this->client->getLastResponse(),
                'businessActivitySummary',
                ['newOrdersCount' => $count],
            ),
        );
    }

    /**
     * @Then I should see :number new customers( in the list)
     */
    public function iShouldSeeNewCustomers(int $count): void
    {
        Assert::true(
            $this->responseChecker->hasValuesInSubresourceObject(
                $this->client->getLastResponse(),
                'businessActivitySummary',
                ['newCustomersCount' => $count],
            ),
            sprintf(
                'There should be %s new customers, but got %s.',
                $count,
                json_encode(
                    $this->responseChecker->getValue($this->client->getLastResponse(), 'businessActivitySummary'),
                ),
            ),
        );
    }

    /**
     * @Then /^there should be total sales of ("[^"]+")$/
     */
    public function thereShouldBeTotalSalesOf(int $totalSales): void
    {
        Assert::true(
            $this->responseChecker->hasValuesInSubresourceObject(
                $this->client->getLastResponse(),
                'businessActivitySummary',
                ['totalSales' => $totalSales],
            ),
        );
    }

    /**
     * @Then /^the average order value should be ("[^"]+")$/
     */
    public function myAverageOrderValueShouldBe(int $averageTotalValue): void
    {
        Assert::true(
            $this->responseChecker->hasValuesInSubresourceObject(
                $this->client->getLastResponse(),
                'businessActivitySummary',
                ['averageOrderValue' => $averageTotalValue],
            ),
            sprintf('Average order value should be %s, but it does not.', $averageTotalValue),
        );
    }

    /**
     * @Then I should see :count new orders in the list
     */
    public function iShouldSeeNewOrdersInTheList(int $count): void
    {
        $this->responseChecker->hasValuesInSubresourceObject(
            $this->client->getLastResponse(),
            'businessActivitySummary',
            ['newOrdersCount' => $count],
        );
    }
}
