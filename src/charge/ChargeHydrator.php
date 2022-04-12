<?php
/**
 * API for Billing
 *
 * @link      https://github.com/hiqdev/billing-hiapi
 * @package   billing-hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\billing\hiapi\charge;

use function count;
use function is_countable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\ChargeState;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use hiqdev\DataMapper\Hydrator\GeneratedHydrator;
use Money\Money;

/**
 * Charge Hydrator.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ChargeHydrator extends GeneratedHydrator
{
    /** {@inheritdoc} */
    public function hydrate(array $data, $object): object
    {
        $data['type']   = $this->hydrator->create($data['type'], Type::class);
        $data['target'] = $this->hydrator->create($data['target'], Target::class);
        $data['action'] = $this->hydrator->create($data['action'], Action::class);
        $data['usage']  = $this->hydrator->create($data['usage'], Quantity::class);
        $data['sum']    = $this->hydrator->create($data['sum'], Money::class);
        if (isset($data['price'])) {
            $data['price'] = $this->hydrator->create($data['price'], PriceInterface::class);
        }
        if (isset($data['bill'])) {
            if ((is_countable($data['bill']) ? count($data['bill']) : 0) === 0) {
                unset($data['bill']); // If empty array or null
            } else if (is_array($data['bill']) && !isset($data['bill']['sum'])) {
                unset($data['bill']); // If array without a "sum"
            } else {
                $data['bill'] = $this->hydrator->create($data['bill'], Bill::class);
            }
        }
        if (isset($data['state'])) {
            $data['state'] = $this->hydrator->create($data['state'], ChargeState::class);
        }
        if (isset($data['parent'])) {
            if (!empty($data['parent']['sum'])) { // If relation is actually populated
                $data['parent'] = $this->hydrate($data['parent'], $this->createEmptyInstance(ChargeInterface::class));
            } else {
                unset($data['parent']);
            }
        }

        return parent::hydrate($data, $object);
    }

    /**
     * {@inheritdoc}
     * @param array
     */
    public function extract($object): array
    {
        $result = array_filter([
            'id'            => $object->getId(),
            'type'          => $this->hydrator->extract($object->getType()),
            'target'        => $this->hydrator->extract($object->getTarget()),
            'action'        => $this->hydrator->extract($object->getAction()),
            'price'         => $object->getPrice() ? $this->hydrator->extract($object->getPrice()) : null,
            'usage'         => $this->hydrator->extract($object->getUsage()),
            'sum'           => $this->hydrator->extract($object->getSum()),
            'bill'          => $object->getBill() ? $this->hydrator->extract($object->getBill()) : null,
            'state'         => $object->getState() ? $this->hydrator->extract($object->getState()) : null,
            'comment'       => $object->getComment(),
        ], static function ($value): bool {
            return $value !== null;
        }, ARRAY_FILTER_USE_BOTH);

        return $result;
    }

    /**
     * @throws \ReflectionException
     * @return object
     */
    public function createEmptyInstance(string $className, array $data = []): object
    {
        return parent::createEmptyInstance(Charge::class, $data);
    }
}
