<?php

namespace hiqdev\billing\hiapi\commands\order;

class CalculateValueCommand extends \hiapi\commands\Command
{
    protected $handlerClass = CalculateOrderValueHandler::class;

    public function rules()
    {
        return [
            ['items', 'each', 'rule' => ['validateItem']],
        ];
    }

    public function validateItem($attribute, $params, $validator)
    {
        var_dump(compact('attribute','params','validator'));
        die;
    }
}