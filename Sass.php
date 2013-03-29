<?php

class Sass extends CApplicationComponent
{

    public $sassPathAlias = 'application.vendor.richthegeek.phpsass';
    public $syntax = 'scss';

    public $cache = false;
    public $debug = false;
    public $debugInfo = false;
    public $lineNumbers = false;
    public $style = 'nested';

    public $functions = array();
    public $extensions = array();
    public $includePaths = array();

    /**
     * @var SassParser
     */
    private $sass;

    /**
     * Constructor
     * @param array Sass options
     * @return Sass
     */
    public function init()
    {
        Yii::import($this->sassPathAlias.'.SassParser');

        $functions = array();
        $paths = array();

        foreach ($this->extensions as $extension) {
            if (class_exists($extension)) {
                $extObj = new $extension();

                if (is_callable(array($extObj, 'getFunctions')))
                    $functions = array_merge($functions, $extObj->getFunctions());

                if (is_callable(array($extObj, 'getIncludePath')))
                    $paths = array_merge($paths, $extObj->getIncludePath());
            } else {
                throw new CException(Yii::t('sass', 'The SASS "{extension}" extension class not registered.', array('{extension}' => $extension)));
            }
        }
        
        foreach ($this->includePaths as $alias) {
            if (file_exists($alias))
                $paths[] = realpath($alias);
            elseif (($path = Yii::getPathOfAlias($alias)) !== false)
                $paths[] = $path;
            else
                throw new CException(Yii::t('saas', 'The include path or alias "{path}" not exists.', array('{path}' => $alias)));
        }
        
        $options = array(
            'cache' => $this->cache,
            'cache_location' => Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'sass_cache',
            'debug' => $this->debug,
            'debug_info' => $this->debugInfo,
            'functions' => array_merge($functions, $this->functions),
            'line_numbers' => $this->lineNumbers,
            'load_paths' => $paths,
            'syntax' => $this->syntax,
            'style' => $this->style,
        );

        $this->sass = new SassParser($options);
    }

    /**
     * Compile SASS and registers a CSS file
     * @param string $file path to scss file
     * @param string $media media that the CSS file should be applied to. If empty, it means all media types.
     * @param boolean $force SASS file will always be compiled if set to true (useful for development mode)
     * @return Sass the Sass object itself (to support method chaining).
     */
    public function registerFile($file, $media = '', $force = FALSE)
    {
        $pathParts = pathinfo($file);

        $dstDir = Yii::app()->assetManager->getPublishedPath($pathParts['dirname']);
        $dstFilename = $pathParts['filename'] . '.css';
        $dstFile = $dstDir . DIRECTORY_SEPARATOR . $dstFilename;

        if (!is_dir($dstDir)) {
            mkdir($dstDir);
            chmod($dstDir, 0777);
        }

        if (!file_exists($dstFile) || $force || !$this->cache)
            file_put_contents($dstFile, $this->sass->toCss($file));

        $url = Yii::app()->assetManager->getPublishedUrl($pathParts['dirname']);
        Yii::app()->clientScript->registerCssFile($url . DIRECTORY_SEPARATOR . $dstFilename);
        return $this;
    }

    /**
     * Returns the extension version number.
     * @return string the version
     */
    public function getVersion()
    {
        return '1.0.1';
    }

}
