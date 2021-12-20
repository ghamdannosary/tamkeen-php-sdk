<?php

namespace PCsoft\Tamkeen\Resources;

class Payment extends Resource
{
    /**
     * The request id of the payment.
     *
     * @var string
     */
    public $RequestId;

    /**
     * The id of the payment.
     *
     * @var string
     */
    public $Status;

    /**
     * The result code of the payment.
     *
     * @var int
     */
    public $ResultCode;

    /**
     * The result message of the payment.
     *
     * @var int
     */
    public $ResultMessage;

    public function isSuccess(): bool
    {
        return $this->ResultCode === -1;
    }

    public function isFailed(): bool
    {
        return !$this->isSuccess();
    }
}
