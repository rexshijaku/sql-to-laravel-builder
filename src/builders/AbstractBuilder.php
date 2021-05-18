<?php

namespace RexShijaku\SQLToLaravelBuilder\builders;

/**
 * This class provides common functionality for all Builder classes.
 * Builder classes are classes which help to construct Query Builder methods.
 *
 * @author Rexhep Shijaku <rexhepshijaku@gmail.com>
 *
 */
abstract class AbstractBuilder
{

    /**
     * Contains options by which Query Builder methods are constructed
     * @var
     */
    protected $options;

    function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Removes surrounding spaces of a string and turns it to lowercase
     * @param $val
     * @return string
     */
    function getValue($val)
    {
        return strtolower(trim($val));
    }

    /**
     * Quotes a keyword for Query Builder
     * @param $str
     * @return string
     */
    function quote($str)
    {
        $str = trim($str, '\"');
        $str = trim($str, '\'');
        $str = addslashes($str);
        return "'" . $str . "'";;
    }

    /**
     *
     * Wraps a value, which involves quoting
     * @param $str
     * @return int|string
     */
    function wrapValue($str)
    {
        if (is_numeric($str))
            return $str;

        if ($this->getValue($str) == 'null')
            return 'null';

        return $this->quote($str);
    }

    /**
     *
     * Tries to convert given parts of WHERE method to php array
     *
     * @param $parts
     * @return false|string
     */
    function arrayify($parts)
    {
        $disposable = array('=' => '');
        $keys = array_keys($disposable);

        $all = array();
        foreach ($parts['fields'] as $k => $field) {
            $operator = $parts['operators'][$k];
            if (in_array($this->getValue($operator), $keys))
                $operator = $disposable[$this->getValue($operator)];

            $value = $parts['values'][$k];
            if (!empty($operator))
                $all[] = '[' . $this->quote($field) . ',' . $this->quote(strtoupper($operator)) . ',' . $this->wrapValue($value) . ']';
            else
                $all[] = '[' . $this->quote($field) . ',' . $this->wrapValue($value) . ']';
        }

        if (!empty($all))
            return "[" . implode(',', $all) . ']';
        else
            return false;
    }

    /**
     * Concatenates tokens by capitalizing the first char of each token starting from the second
     * Creates a method name compatible to Query Builder
     *
     * @param $tokens
     * @return string
     */
    function fnMerger($tokens)
    {
        $separator = '';
        for ($i = 0; $i < count($tokens); $i++)
            if ($i > 0)
                $tokens[$i] = ucfirst($tokens[$i]);
        return implode($separator, $tokens);
    }

    /**
     * Removes surrounding brackets of a given value
     * @param $value
     * @return string
     */
    protected function unBracket($value)
    {
        if ($value[0] == '(')
            $value = substr($value, 1);
        if ($value[strlen($value) - 1] == ')')
            $value = substr($value, 0, strlen($value) - 1);
        return $value;
    }

    /**
     * Builds a rawable Query Builder method
     *
     * @param $val
     * @param false $is_raw
     * @return int|string
     */
    protected function buildRawable($val, $is_raw = false)
    {
        $val = $this->wrapValue($val);
        return $is_raw ? $this->options['facade'] . 'raw(' . $val . ')' : $val;
    }
}