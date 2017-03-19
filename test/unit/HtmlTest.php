<?php

use Rapture\Helper\Html;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    public function testIterate()
    {
        Html::iterate('class', ['odd', 'even']);

        $this->assertEquals('odd', Html::iterate('class'));
        $this->assertEquals('even', Html::iterate('class'));
        $this->assertEquals('odd', Html::iterate('class'));
        $this->assertEquals('even', Html::iterate('class'));
    }
}
