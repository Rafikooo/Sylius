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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class PromotionCouponEligibility extends Constraint
{
    public string $invalid = 'sylius.promotion_coupon.is_invalid';

    public string $expired = 'sylius.promotion_coupon.expired';

    public string $ineligible = 'sylius.promotion_coupon.ineligible';

    public function validatedBy(): string
    {
        return 'sylius_api_promotion_coupon_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
