<?php

namespace Rapture\Helper;

use Rapture\Form\Element;
use Rapture\Form\Select;

/**
 * Html Helper
 *
 * @package Rapture\Helper
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Html
{
    protected static $iterate = [];

    /**
     * @param string $name    Key name
     * @param array  $through List of names
     *
     * @return string
     */
    public static function iterate($name, array $through = [])
    {
        if ($through) {
            self::$iterate[$name] = $through;

            return null;
        }

        $result = array_shift(self::$iterate[$name]);
        array_push(self::$iterate[$name], $result);

        return $result;
    }

    /*
     * Form
     */

    /**
     * renderAttributes
     *
     * @param array $attributes Key value attributes
     *
     * @return string
     */
    public static function attributes(array $attributes)
    {
        ksort($attributes);

        $xhtml = '';
        foreach ($attributes as $attribute => $value) {
            if (is_object($value)) {
                $value = '[object]';
            }
            if (is_array($value)) {
                $value = current($value);
            }
            $xhtml .= isset($value)
                ? htmlentities($attribute, ENT_QUOTES) . '="' . htmlentities($value, ENT_QUOTES) . '" '
                : '';
        }

        return rtrim($xhtml);
    }

    /**
     * Render html tag
     *
     * @param string $tag        Tag name
     * @param array  $attributes Tag attributes
     * @param string $html       Tag html
     *
     * @return string
     */
    public static function tag($tag, array $attributes = [], $html = '')
    {
        $xhtmlAttributes = self::attributes($attributes);

        return ($tag == 'input')
            ? "<input {$xhtmlAttributes} />"
            : "<{$tag} {$xhtmlAttributes}>{$html}</{$tag}>";
    }

    /**
     * Render label
     *
     * @param Element $element Form element
     *
     * @return string
     */
    public static function label(Element $element)
    {
        $meta = $element->getMeta();
        $attr = $element->getAttributes();

        $label = isset($meta['label'][0]) ? $meta['label'] : $attr['name'];
        $for = isset($attr['id']) ? $attr['id'] : $attr['name'];
        $class = isset($meta['label-class']) ? $meta['label-class'] : '';

        return self::tag('label', ['for' => $for, 'class' => $class], $label);
    }

    /**
     * Render select options
     *
     * @param array $options  Options array
     * @param mixed $selected Selected values ex: [value => !NULL]
     *
     * @return string
     */
    public static function options($options, $selected = [])
    {
        $xhtml = '';

        if ($options instanceof \Closure) {
            $options = $options();
        }

        foreach ($options as $value => $name) {
            // optgroup
            if (is_array($name)) {
                $xhtml .= '<optgroup label="' . htmlentities($value, ENT_QUOTES) . '">';
                $xhtml .= self::options($name, $selected);
                $xhtml .= '</optgroup>';
            } else {
                $xhtml .= '<option value="'
                    . htmlentities($value, ENT_QUOTES)
                    . (isset($selected[$value]) ? '" selected="selected">' : '">')
                    . htmlentities($name, ENT_QUOTES)
                    . '</option>';
            }
        }

        return $xhtml;
    }

    /**
     * help
     *
     * @param Element $element Element
     * @param string  $class   Class for help block
     *
     * @return string
     */
    public static function help(Element $element, $class = 'help-block')
    {
        $help = $element->getMeta()['help'] ?? '';

        return $help ? "<span class=\"{$class}\">{$help}</span>" : '';
    }

    /**
     * Render an element
     *
     * @param Element $element Form element
     *
     * @return string
     */
    public static function element(Element $element)
    {
        $meta = $element->getMeta() + ['before' => '', 'after' => ''];

        $html = $element instanceof Select
            ? self::options($element->getOptions(), array_flip((array)$element->getValue()))
            : $meta['html'];

        if (isset($meta['template'])) {
            return sprintf(
                $meta['template'],
                self::tag($element->getTag(), $element->getAttributes(), $html)
            );
        }

        return $meta['before'] . self::tag($element->getTag(), $element->getAttributes(), $html) . $meta['after'];
    }
}
