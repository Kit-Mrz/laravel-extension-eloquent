<?php

namespace Mrzkit\LaravelExtensionEloquent\Partitions\Contracts;

use Mrzkit\LaravelExtensionEloquent\Partitions\Partition;

/**
 * 分表模型
 */
interface PartitionContract
{
    /**
     * @desc 获取分表数
     * @return int
     */
    public function getPartitionCount() : int;

    /**
     * @desc 获取分表配置
     * @return array
     */
    public function getPartitionConfig() : array;

    /**
     * @desc 获取分表实例
     * @return Partition
     */
    public function getPartitionInstance() : Partition;
}
