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

namespace Sylius\Tests\Api\Admin;

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\Address;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class SalesStatisticsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    /** @test */
    public function it_gets_dashboard_statistics_data(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        for ($i = 0; $i < 3; ++$i) {
            $this->placeOrder('ORDERTOKEN'.$i, sprintf('customer_%s@example.com', $i));
            $this->payOrder('ORDERTOKEN'.$i);
        }

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/sales-statistics',
            server: $this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/sales_statistics/get_sales_statistics_response',
            Response::HTTP_OK
        );
    }
}
