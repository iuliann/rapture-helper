<?php

namespace Rapture\Helper;

/**
 * Template assets manager
 *
 * @package Rapture\Helper
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Assets
{
    /** @var string */
    protected $urlPath = '/assets/';

    /** @var string */
    protected $dirPath = './public/assets/';

    /** @var array */
    protected $assets = [];

    /** @var array */
    protected $jsInline = [];

    /** @var array */
    protected $cssInline = [];

    /** @var array */
    protected $jsGlobals = [];

    /**
     * Asset constructor.
     *
     * @param string $url       URL base path
     * @param string $dir       Directory root path
     * @param array  $assets    Assets list
     * @param string $inlineJs  Inline JS
     * @param string $inlineCss Inline CSS
     * @param array  $globalJs  Global JS
     */
    public function __construct($url = '/assets/', $dir = './public/assets/', array $assets = [], $inlineJs = '', $inlineCss = '', $globalJs = [])
    {
        $this->setUrlPath($url);
        $this->setDirPath($dir);
        $this->add($assets);
        $this->addJsInline($inlineJs);
        $this->addCssInline($inlineCss);
        $this->addJsGlobals($globalJs);
    }

    /**
     * Set base URL for assets
     *
     * @param string $path Base url for assets
     *
     * @return Assets
     */
    public function setUrlPath(string $path = '/assets/'):Assets
    {
        $this->urlPath = '/' . trim($path, '/') . '/';

        return $this;
    }

    /**
     * @param string $path Directory path
     *
     * @return Assets
     */
    public function setDirPath(string $path = '/'):Assets
    {
        $this->dirPath = rtrim($path, '/') . '/';

        return $this;
    }

    /**
     * Append inline css
     *
     * @param string $css Inline CSS
     *
     * @return Assets
     */
    public function addCssInline(string $css = ''):Assets
    {
        if ($css) {
            array_push($this->cssInline, $css);
        }

        return $this;
    }

    /**
     * Append inline js
     *
     * @param string $js Inline JS
     *
     * @return Assets
     */
    public function addJsInline(string $js = ''):Assets
    {
        if ($js) {
            array_push($this->jsInline, $js);
        }

        return $this;
    }

    /**
     * Add assets
     *
     * Single file:
     * $this->add(['styles' => 'css/style.css']);
     *
     * @param array $assets Assets
     *
     * @return $this
     */
    public function add(array $assets)
    {
        foreach ((array)$assets as $group => $asset) {
            if (is_array($asset)) {
                $this->add($asset);
                continue;
            }

            $this->assets[] = $asset;
        }

        return $this;
    }

    /**
     * Add JS globals
     *
     * @param array $js Array as key => value
     *
     * @return $this
     */
    public function addJsGlobals(array $js = [])
    {
        $this->jsGlobals = $js + $this->jsGlobals;

        return $this;
    }

    /*
    * Render functions
    */

    /**
     * RenderGlobalJs
     *
     * @param string $template After global js
     *
     * @return string
     */
    public function renderJsGlobals($template = '<script type="text/javascript">%s</script>')
    {
        $js = '';
        foreach ($this->jsGlobals as $name => $value) {
            $js .= sprintf("\tvar %s = %s;\n", $name, str_replace(['"{JS}', '{JS}"'], ['', ''], json_encode($value)));
        }

        return sprintf($template, $js);
    }

    /**
     * Render JS alias for $this->render('js');
     *
     * @param string $template Tag template
     *
     * @return string
     */
    public function renderJs($template = '<script type="text/javascript" src="%s"></script>')
    {
        $html = '';
        foreach ($this->assets as $asset) {
            if (is_array($asset)) {
                $html .= $this->renderJs($asset);
                continue;
            }

            if (strpos($asset, '.js') !== false) {
                $html .= (substr($asset, 0, 4) !== 'http' && $asset[0] != '/')
                    ? sprintf($template, $this->urlPath . $asset)
                    : sprintf($template, $asset);
            }
        }

        return $html;
    }

    /**
     * Render CSS alias for $this->render('css');
     *
     * @param string $template Tag template
     *
     * @return string
     */
    public function renderCss($template = '<link rel="stylesheet" type="text/css" href="%s" />')
    {
        $html = '';
        foreach ($this->assets as $asset) {
            if (is_array($asset)) {
                $html .= $this->renderCss($asset);
                continue;
            }

            if (strpos($asset, '.css') !== false) {
                $html .= (substr($asset, 0, 4) !== 'http' && $asset[0] != '/')
                    ? sprintf($template, $this->urlPath . $asset)
                    : sprintf($template, $asset);
            }
        }

        return $html;
    }

    /**
     * Render inline JS
     *
     * @param string $template Tag template
     *
     * @return string
     */
    public function renderJsInline($template = '<script type="text/javascript">%s</script>')
    {
        return sprintf($template, implode("\n\n", $this->jsInline));
    }

    /**
     * Render inline CSS
     *
     * @param string $template Tag template
     *
     * @return string
     */
    public function renderCssInline($template = '<style type="text/css" media="all">%s</style>')
    {
        return sprintf($template, implode("\n\n", $this->cssInline));
    }

    /**
     * Remove assets
     *
     * @param mixed $assets Array with assets or a single asset
     *
     * @return $this
     */
    public function remove(array $assets)
    {
        foreach ($assets as $name) {
            unset($this->assets[$name]);
        }

        return $this;
    }
}
