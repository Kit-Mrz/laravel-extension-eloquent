<?php
namespace Mrzkit\LaravelExtensionEloquent\Contracts;

use Mrzkit\LaravelExtensionEloquent\Model\EloquentModel;

/**
 * 模型契约
 */
interface ModelContract
{
    /**
     * @desc 设置模型
     * @param EloquentModel $model
     * @return $this
     */
    public function setModel($model);

    /**
     * @desc 获取模型
     * @return EloquentModel
     */
    public function getModel();
}
