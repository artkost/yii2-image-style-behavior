<?php

namespace artkost\imagestyle;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;

/**
 * Class ImageStyleBehavior
 * Image styles file behavior.
 *
 * @author Nikolay Kostyurin <nikolay@artkost.ru>
 * @since 2.0
 *
 * To use this behavior define
 * ```php
 * public function behaviors()
 * {
 *     return array_merge(parent::behaviors(), [
 *         'styles' => [
 *             'class' => ImageStyleBehavior::className(),
 *             'path' => $this->stylesPath(),
 *             'url' => $this->stylesUrl(),
 *             'attribute' => 'uri',
 *             'styles' => [
 *                 'big' => [$this, 'styleBig']
 *             ]
 *         ]
 *     ]);
 * }
 * ```
 *
 * And style creation method
 * ```php
 * public function styleBig()
 * {
 *     return Image::thumbnail($this->filePath, 814, 458)->save($this->style('big')->path);
 * }
 * ```
 *
 * Behavior also provides method `style`, that returns ImageStyle instance
 *
 * ```php
 *  $model->style('big')->url
 * ```
 *
 * @property ActiveRecord $owner
 */
class ImageStyleBehavior extends Behavior
{
    /**
     * File path attribute
     * @var string
     */
    public $attribute;

    /**
     * Url to styles dir
     * @var string
     */
    public $url;

    /**
     * Path to styles dir
     * @var string
     */
    public $path;

    /**
     * Styles callbacks
     * @var array
     */
    public $styles = [];

    /**
     * @var ImageStyle[]
     */
    protected $instances = [];

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (! $this->attribute) {
            throw new InvalidParamException("Invalid or empty 'attribute' value");
        }

        if (! $this->url) {
            throw new InvalidParamException("Invalid or empty 'url' value");
        }

        if (! $this->path) {
            throw new InvalidParamException("Invalid or empty 'path' value");
        }

        if (! is_array($this->styles) || empty($this->styles)) {
            throw new InvalidParamException("Invalid or empty 'styles' array");
        }

        foreach ($this->styles as $name => $handler) {
            $this->instances[$name] = Yii::createObject([
                'class' => ImageStyle::className(),
                'dirPath' => $this->path,
                'dirUrl' => $this->url,
                'name' => $name,
                'handler' => $handler,
                'behavior' => $this
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
        ];
    }

    /**
     * @param $name
     * @return ImageStyle|null
     */
    public function style($name)
    {
        return isset($this->instances[$name]) ? $this->instances[$name] : null;
    }

    public function getFilePath()
    {
        return $this->owner->getAttribute($this->attribute);
    }

    public function afterInsert()
    {
        foreach ($this->instances as $name => $style) {
            $style->save();
        }
    }

    public function afterUpdate()
    {
        foreach ($this->instances as $name => $style) {
            $style->save();
        }
    }

    public function afterDelete()
    {
        foreach ($this->instances as $name => $style) {
            $style->delete();
        }
    }
}
