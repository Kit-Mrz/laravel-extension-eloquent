<?php

namespace Mrzkit\LaravelExtensionEloquent\Partitions;

use RuntimeException;

class Partition
{
    /**
     * @var array 分表配置
     */
    protected $partitionConfig = [];

    /**
     * @var int 分表计算因子
     */
    protected $partitionFactor = 0;

    /**
     * @var int 分表数
     */
    protected $partitionCount = 0;

    /**
     * @desc 设置分表数
     * @param int $partitionCount
     * @return $this
     */
    public function setPartitionCount(int $partitionCount)
    {
        if ($partitionCount < 1) {
            throw new RuntimeException('请设置正确的分表数，值必须大于0!');
        }

        $this->partitionCount = $partitionCount;

        return $this;
    }

    /**
     * @desc 获取分表数
     * @return int
     */
    public function getPartitionCount() : int
    {
        if ($this->partitionCount < 1) {
            throw new RuntimeException('请设置正确的分表数，值必须大于0!');
        }

        return $this->partitionCount;
    }

    /**
     * @desc 设置分表计算因子
     */
    public function setPartitionFactor(int $partitionFactor)
    {
        if ($partitionFactor < 0) {
            throw new RuntimeException('请设置正确的分表计算因子，值必须大于等于0!');
        }

        $this->partitionFactor = $partitionFactor;

        return $this;
    }

    /**
     * @desc 获取分表计算因子
     * @return int
     */
    public function getPartitionFactor()
    {
        if ($this->partitionFactor < 0) {
            throw new RuntimeException('请设置正确的分表计算因子，值必须大于等于0!');
        }

        return $this->partitionFactor;
    }

    /**
     * @desc 设置分表配置
     * @param array $partitionConfig
     * @return $this
     */
    public function setPartitionConfig(array $partitionConfig)
    {
        if (empty($partitionConfig)) {
            throw new RuntimeException('请在模型中设置分表表配置 Model::partitionConfig!');
        }

        foreach ($partitionConfig as $item) {
            if ( !isset($item['low'])) {
                throw new RuntimeException('low Not Default.');
            }

            if ( !isset($item['high'])) {
                throw new RuntimeException('high Not Default.');
            }
            if ( !isset($item['partition'])) {
                throw new RuntimeException('partition Not Default.');
            }
        }

        $this->partitionConfig = $partitionConfig;

        return $this;
    }

    /**
     * @desc 获取分表配置
     * @return array
     */
    public function getPartitionConfig() : array
    {
        if (empty($this->partitionConfig)) {
            throw new RuntimeException('请设置分表配置!');
        }

        return $this->partitionConfig;
    }

    /**
     * @desc 计算分表
     * @return int
     */
    public function calculatePartition()
    {
        $pos = $this->calculateFactor();

        // 获取分表配置
        $partitionConfig = $this->getPartitionConfig();

        $partition = 0;

        foreach ($partitionConfig as $item) {
            if ($pos >= $item['low'] && $pos <= $item['high']) {
                $partition = $item['partition'];
                break;
            }
        }

        if ($partition < 1 || $partition > $this->getPartitionCount()) {
            throw new RuntimeException('分表计算错误，请检查分表配置!');
        }

        return $partition;
    }

    /**
     * @desc 计算因子
     * @return int
     */
    public function calculateFactor()
    {
        $partitionCount = $this->getPartitionCount();

        $partitionFactor = $this->getPartitionFactor();

        $pos = $partitionFactor % $partitionCount;

        return $pos;
    }
}
