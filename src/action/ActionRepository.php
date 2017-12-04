<?php
/**
 * API for Billing
 *
 * @link      https://github.com/hiqdev/billing-hiapi
 * @package   billing-hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\billing\hiapi\action;

use hiapi\db\CallExpression;
use hiapi\db\HstoreExpression;
use hiapi\components\EntityManagerInterface;
use hiapi\repositories\BaseRepository;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\action\ActionFactoryInterface;
use hiqdev\php\billing\action\ActionQuery;
use hiqdev\php\billing\sale\Sale;

class ActionRepository extends BaseRepository
{
    public $queryClass = ActionQuery::class;

    /**
     * @var ActionFactory
     */
    protected $factory;

    public function __construct(
        EntityManagerInterface $em,
        ActionFactoryInterface $factory,
        array $config = []
    ) {
        parent::__construct($config);

        $this->em = $em;
        $this->factory = $factory;
    }

    public function save(ActionInterface $action)
    {
        $sale = $action->getSale();
        $hstore = new HstoreExpression(array_filter([
            'id'        => $action->getId(),
            'object_id' => $action->getTarget()->getId(),
            'type'      => $action->getType()->getName(),
            'type_id'   => $action->getType()->getId(),
            'amount'    => $action->getQuantity()->getQuantity(),
            'sale_id'   => $sale ? $this->em->findId($sale) : null,
        ]));
        $call = new CallExpression('replace_action', [$action->getId(), $hstore]);
        $command = $this->em->getConnection()->createSelect($call);
        $action->setId($command->scalar());
    }
}