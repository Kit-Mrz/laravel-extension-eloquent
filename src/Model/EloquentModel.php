<?php

namespace Mrzkit\LaravelExtensionEloquent\Model;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentModel extends Model
{
    /**
     * @var string 时间格式
     */
    protected $serializeDateFormat;

    /**
     * @var string 与表关联的主键
     */
    protected $primaryKey = 'id';

    /**
     * @var string 模型日期的存储格式
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * @var array 默认值属性
     */
    protected $attributes = [];

    /**
     * @var array 可以批量赋值的属性
     */
    protected $fillable = [];

    /**
     * @var array 映射事件
     */
    protected $dispatchesEvents = [];

    /**
     * $fields = [
     *      [
     *          'comment' => '主键',
     *          'snake'   => 'id',
     *          'camel'   => 'id',
     *       ]
     * ]
     * @var array 表字段集合
     */
    protected static $fields = [];

    /**
     * @desc 下划线字段
     * @return array
     */
    public static function getSnake()
    {
        return collect(static::$fields ?? [])->pluck('snake')->toArray();
    }

    /**
     * @desc 驼峰字段
     * @return array
     */
    public static function getCamel()
    {
        return collect(static::$fields ?? [])->pluck('camel')->toArray();
    }

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
