SASS renderer extension for Yii Framework
==================

This extension allows you to use [SASS](http://sass-lang.com/) css templates in Yii.

###Resources
* [Sass php renderer](https://github.com/richthegeek/phpsass)
* [Sass](http://sass-lang.com/)
* [Report a bug](https://github.com/4you4ever/yii-sass/issues)

###Requirements
* Yii 1.0 or above

###Installation
* Extract the release file under `protected/extensions`.
* [Download](https://github.com/richthegeek/phpsass) and extract all PhpSass files from under `vendor/richthegeek/phpsass/`.
* Add the following to your config file 'components' section:

```php
<?php
    'sass' => array(
        'class' => 'ext.Sass',
        
        // All parameters below are optional, change them to your needs
        'cache' => false,
        'debug' => false,
        'extensions' => array(
          'Compass' //not included by default
        ),
        'functions' => array(
          'alias' => callable
        ),
        'includePaths' => array(
            'path.to.search.your.sass.files'
        ),
        'syntax' => 'scss',
    ),
```

###Usage
* See [Sass syntax](http://sass-lang.com/docs/yardoc/file.SASS_REFERENCE.html#syntax).
 
###Usage example
To compile and connect result css file use sass component:

```php
<?php
    Yii::app()->sass->registerFile($file, $media, $force = false)
```
