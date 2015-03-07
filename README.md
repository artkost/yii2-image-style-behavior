# Image style generate behavior for Yii 2

Create sets of styles for your images

## Installation

The preferred way to install this extension is through composer.

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require artkost/yii2-image-style-behavior
```

or add

```
"artkost/yii2-image-style-behavior": "*"
```

to the `require` section of your `composer.json` file.

## Configuring

Configure model as follows

```php
use artkost\imagestyle\ImageStyleBehavior;

class ImageFile extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'styles' => [
                'class' => ImageStyleBehavior::className(),
                'path' => $this->stylesPath(),
                'url' => $this->stylesUrl(),
                'attribute' => 'uri',
                'styles' => [
                    'big' => [$this, 'styleBig'],
                    'small' => [$this, 'styleSmall']
                ]
            ]
        ];
    }

    /**
     * @return \Imagine\Image\ManipulatorInterface
     */
    public function styleBig()
    {
        return Image::thumbnail($this->filePath, 814, 458)->save($this->style('big')->path);
    }
}
```

## Usage

```php
$file = ImageFile::findOne($id);
echo $file->style('big')->url;
```
