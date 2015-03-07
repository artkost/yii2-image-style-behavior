<?php

namespace artkost\imagestyle;

use yii\base\Component;
use yii\base\InvalidCallException;
use yii\helpers\FileHelper;

/**
 * Class ImageStyle
 *
 * @property string $filePath
 */
class ImageStyle extends Component
{
    /**
     * @var string
     */
    public $dirUrl;

    /**
     * @var string
     */
    public $dirPath;

    /**
     * @var \Closure
     */
    public $handler;

    /**
     * @var string
     */
    public $name;

    /**
     * @var ImageStyleBehavior
     */
    public $behavior;

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->behavior->getFilePath();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->dirUrl . '/' . $this->name . '/' . $this->filePath;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->dirPath . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . $this->filePath;
    }

    /**
     * @return string
     */
    public function getDirPath()
    {
        return dirname($this->getPath());
    }

    /**
     * @return bool
     */
    public function isExists()
    {
        return is_file($this->getPath());
    }

    /**
     * @return bool status
     */
    public function save()
    {
        if (! FileHelper::createDirectory($this->getDirPath())) {
            throw new InvalidCallException("Directory '{$this->getDirPath()}' doesn't exist or cannot be created.");
        }

        if (is_callable($this->handler)) {
            return call_user_func_array($this->handler, [$this]);
        } else {
            throw new InvalidCallException("Style function for {$this->name} not callable");
        }
    }

    public function delete()
    {
        if ($this->isExists()) {
            unlink($this->getPath());
        }
    }
}
