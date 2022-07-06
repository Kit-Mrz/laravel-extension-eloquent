<?php

namespace Mrzkit\LaravelExtensionEloquent;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentModel extends Model
{
    /**
     * @var string 时间格式
     */
    protected $serializeDateFormat;

    /**
     * 格式化日期
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->serializeDateFormat ?? 'Y-m-d H:i:s');
    }

    /**
     * 获取给定数组的可填充属性
     *
     * @param array $attributes
     * @return array
     */
    public function overwriteFillableFromArray(array $attributes) : array
    {
        return parent::fillableFromArray($attributes);
    }
}

