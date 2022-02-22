<?php

namespace Mrzkit\LaravelExtensionEloquent\Model;

use InvalidArgumentException;
use Mrzkit\LaravelExtensionEloquent\Partition;

trait Positioner
{
    /**
     * @var int 分表因子
     */
    protected $factor = null;

    /**
     * @desc 获取分表实例
     * @return Partition
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getShardingInstance() : Partition
    {
        app()->singletonIf(Partition::class);

        return app()->make(Partition::class);
    }

    /**
     * @desc 定位
     * @param int $id
     */
    public function position(int $id)
    {
        $shardMaxCount = $this->getShardMaxCount();

        $shardConfig = $this->getShardConfig();

        $sharding = $this->getShardingInstance();

        $location = $sharding->setPartitionMaxCount($shardMaxCount)->setPartitionFactor($id)
            ->setPartitionConfig($shardConfig)->calculatePartition();

        return $location;
    }

    /**
     * @desc 选表
     * @param int $location
     * @return $this
     */
    public function selectTable(int $location)
    {
        $tableName = $this->getTable();

        $tableName = preg_replace('/_\d+$/', '', $tableName);

        $tableName = $tableName . "_{$location}";

        $this->setTable($tableName);

        return $this;
    }

    /**
     * @desc 分表
     * @return ShardModel
     */
    public function sharding()
    {
        $location = $this->position($this->getFactor());

        return $this->selectTable($location);
    }

    /**
     * @desc 设置分表因子
     * @param int $factor
     * @return $this
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * @desc 获取分表因子
     * @return int
     */
    public function getFactor()
    {
        if (is_null($this->factor)) {
            throw new InvalidArgumentException('缺少分表因子');
        }

        $factor = $this->factor;

        return $factor;
    }
}
