<?php
/**
 * API for Billing
 *
 * @link      https://github.com/hiqdev/billing-hiapi
 * @package   billing-hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\billing\hiapi\vo;

use hiqdev\billing\hiapi\Hydrator\Strategy\MoneyStrategy;
use hiqdev\DataMapper\Hydrator\GeneratedHydrator;
use Money\Currency;
use Money\Money;

/**
 * Class MoneyHydrator.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 * @deprecated Use {@see MoneyStrategy} instead
 */
class MoneyHydrator extends GeneratedHydrator
{
    public function hydrate(array $data, $object): object
    {
        $currency = new Currency(strtoupper($data['currency']));

        return new Money($data['amount'] ?? '0', $currency);
    }

    /**
     * {@inheritdoc}
     * @param object|Money $object
     */
    public function extract($object): array
    {
        return [
            'currency'  => $object->getCurrency()->getCode(),
            'amount'    => $object->getAmount(),
        ];
    }
}
