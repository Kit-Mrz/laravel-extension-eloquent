<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

interface ShardingContract
{
    /**
     * @desc 获取分表数
     * @return int
     */
    public function getShardCount() : int;

    /**
     * @desc 获取分表配置
     * @return array
     */
    public function getShardConfig() : array;

}
