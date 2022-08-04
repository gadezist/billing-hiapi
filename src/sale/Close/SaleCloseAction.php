<?php
/**
 * API for Billing
 *
 * @link      https://github.com/hiqdev/billing-hiapi
 * @package   billing-hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\billing\hiapi\sale\Close;

use hiapi\exceptions\domain\RequiredInputException;
use hiqdev\php\billing\sale\SaleRepositoryInterface;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\sale\Sale;
use DomainException;

class SaleCloseAction
{
    private SaleRepositoryInterface $repo;

    public function __construct(SaleRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function __invoke(SaleCloseCommand $command): Sale
    {
        $this->checkRequiredInput($command);

        $sale = new Sale(
            null,
            $command->target,
            $command->customer,
            new Plan($command->plan?->getId(), null)
        );
        $saleId = $this->repo->findId($sale);
        if (!$saleId) {
            throw new DomainException("Sale does not exists");
        }

        $sale = $this->repo->findById($saleId);
        $sale->close($command->time);

        $this->repo->save($sale);

        return $sale;
    }

    protected function checkRequiredInput(SaleCloseCommand $command)
    {
        if (empty($command->customer)) {
            throw new RequiredInputException('customer');
        }
        if (empty($command->target)) {
            throw new RequiredInputException('target');
        }
    }
}
