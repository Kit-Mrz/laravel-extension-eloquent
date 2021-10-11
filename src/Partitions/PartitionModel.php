<?php

namespace Mrzkit\LaravelExtensionEloquent\Partitions;

use Mrzkit\LaravelExtensionEloquent\Partitions\Contracts\PartitionContract;
use Mrzkit\LaravelExtensionEloquent\EloquentModel;

abstract class PartitionModel extends EloquentModel implements PartitionContract
{
    /**
     * @var int 分表数
     */
    protected $partitionCount = 64;

    /**
     * @var \int[][] 分表配置
     */
    protected $partitionConfig = [
        [
            'partition' => 8,
            'low'       => 0,
            'high'      => 7,
        ],
        [
            'partition' => 16,
            'low'       => 8,
            'high'      => 15,
        ],
        [
            'partition' => 24,
            'low'       => 16,
            'high'      => 23,
        ],
        [
            'partition' => 32,
            'low'       => 24,
            'high'      => 31,
        ],
        [
            'partition' => 40,
            'low'       => 32,
            'high'      => 39,
        ],
        [
            'partition' => 48,
            'low'       => 40,
            'high'      => 47,
        ],
        [
            'partition' => 56,
            'low'       => 48,
            'high'      => 55,
        ],
        [
            'partition' => 64,
            'low'       => 56,
            'high'      => 63,
        ],
    ];

    /**
     * @desc 获取分表数
     * @return int $this->partitionCount
     */
    abstract public function getPartitionCount() : int;

    /**
     * @desc 获取分表配置
     * @return array $this->partitionConfig
     */
    abstract public function getPartitionConfig() : array;

    /**
     * @desc 获取分表实例
     * @return Partition
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getPartitionInstance() : Partition
    {
        app()->singletonIf(Partition::class);

        return app()->make(Partition::class);
    }

    /**
     * @desc 选表(表定位)
     * @param int $id
     */
    public function partition(int $id)
    {
        $partitionCount = $this->getPartitionCount();

        $partitionConfig = $this->getPartitionConfig();

        $partition = $this->getPartitionInstance();

        $location = $partition->setPartitionCount($partitionCount)->setPartitionFactor($id)
            ->setPartitionConfig($partitionConfig)->calculatePartition();

        $tableName = $this->getTable();

        $tableName = preg_replace('/_\d+$/', '', $tableName);

        $tableName = $tableName . "_{$location}";

        $this->setTable($tableName);

        return $this;
    }
}
