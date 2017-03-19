<?php

class AssetsTest extends \PHPUnit_Framework_TestCase
{
    public function testAssets()
    {
        $assets = new \Rapture\Template\Assets();
        $assets->addJsGlobals(['version' => 'v1.0.1'])
               ->addJsInline('alert("Test");')
               ->addCssInline('* {margin:0 !important}')
               ->add([
                    'style' =>  'css/main.css',
                    'jquery'=>  'js/jquery.js'
                ]);

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/assets/css/main.css" />',$assets->renderCss());
        $this->assertEquals('<script type="text/javascript" src="/assets/js/jquery.js"></script>',  $assets->renderJs());

        $this->assertEquals('<script type="text/javascript">alert("Test");</script>',               $assets->renderJsInline());
        $this->assertEquals('<style type="text/css" media="all">* {margin:0 !important}</style>',   $assets->renderCssInline());
    }
}
