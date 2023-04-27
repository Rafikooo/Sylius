<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithCodeTrait
{
    public function withCode(string $code): self
    {
        return $this->addState(['code' => $code]);
    }
}
