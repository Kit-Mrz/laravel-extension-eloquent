<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

interface ShardFactorContract
{
    /**
     * @desc 设置分表因子
     * @param $factor
     * @return mixed
     */
    public function setFactor($factor);

    /**
     * @desc 获取分表因子
     * @return mixed
     */
    public function getFactor();
}
