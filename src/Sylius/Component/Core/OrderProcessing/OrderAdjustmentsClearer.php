<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderAdjustmentsClearer implements OrderProcessorInterface
{
    /**
     * @var array
     */
    private $adjustmentsToRemove;

    public function __construct(array $adjustmentsToRemove = [])
    {
        $this->adjustmentsToRemove = $adjustmentsToRemove;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order): void
    {
        foreach ($this->adjustmentsToRemove as $type) {
            $order->removeAdjustmentsRecursively($type);
        }
    }
}
