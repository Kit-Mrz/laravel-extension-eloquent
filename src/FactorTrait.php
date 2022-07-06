<?php

namespace Mrzkit\LaravelExtensionEloquent;

trait FactorTrait
{
    /**
     * @var int
     */
    protected $factorId;

    /**
     * @return int
     */
    public function getFactorId() : int
    {
        return $this->factorId;
    }

    /**
     * @param int $factorId
     */
    public function setFactorId(int $factorId)
    {
        $this->factorId = $factorId;

        return $this;
    }
}
