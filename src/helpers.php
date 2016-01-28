<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 12/30/2015
 * Time: 3:40 PM
 */

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!function_exists('e')) {
    /**
     * Blatant ripoff of Laravel and Twig's HTML escape function
     *
     * @param $string string The string to escape
     *
     * @return string The escaped string
     */
    function e($string)
    {
        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
    }
}


if (!function_exists('messages_path')) {
    /**
     * Determine the path to the 'messages' directory as best as possible
     *
     * @return string The best guess for where the messages are
     */
    function messages_path()
    {
        $module_path = __DIR__ . DS . "locale";
        if (!file_exists($module_path)) {
            $module_path = "/usr/share/locale";
        }
        return $module_path;
    }
}

if (!function_exists('get_builtin_gnu_translations')) {
    /**
     * Returns the built-in GNU translations located in php-wtform's
     * src root (src/locale/[languages here]
     * @param null $languages Which language to get
     * @return \Gettext\GettextTranslator The resulting translation object
     */
    function get_builtin_gnu_translations($languages = null)
    {
        if (is_null($languages)) {
            $language = "en_US";
        }
        $domain = messages_path();
        $t = new \Gettext\GettextTranslator();
        $t->setLanguage($language);
        $t->loadDomain('wtforms', $domain);
        return $t;
    }
}

if (!function_exists('get_translations')) {
    /**
     * Gets a translation object
     * @param null $languages
     * @param null $getter
     * @return \Gettext\GettextTranslator
     */
    function get_translations($languages = null, $getter = null)
    {
        // TODO: Implement user-defined callback getter for translations
        return get_builtin_gnu_translations($languages);
    }
}

if (!function_exists('html_params')) {
    /**
     * Generate HTML attribute syntax from inputted keyword arguments.
     *
     * The output value is sorted by the passed keys, to provide consistent output
     * each time this function is called with the same parameters. Because of the
     * frequent use of the normally reserved keywords ``class`` and ``for``, suffixing
     * these with an underscore will allow them to be used.
     *
     * In order to facilitate the use of 'data-' attributes, the first underscore
     * behind the ``data``-element is replaced with a hyphen.
     *
     * ```
     * >>> html_params(['any_data_attribute'=>'something'])
     * 'data-any_attribute="something"'
     * ```
     * In addition, the values ``true`` and ``false`` are special:
     *   * ``'attr'=>true`` generates the HTML compact output of a boolean attribute,
     *   * ``'attr'=>false`` will be ignored and generate no output
     * ```
     * >>> html_params(['name'=>'text1', 'id'=>'f', 'class_'=>'text'])
     * 'class="text" id="f" name="text1"
     * >>> html_params(['checked'=>true, 'readonly'=>false, 'name'=>'text1', 'abc'=>'hello'])
     * 'abc="hello" checked name="text1"
     *
     * @param array $kwargs An associative array of attributes for an HTML tag
     * @return string The string of HTML attributes
     */
    function html_params($kwargs)
    {
        $params = [];
        sort($kwargs);
        foreach ($kwargs as $key => $value) {
            if (in_array($key, ['class_', 'class__', 'for_'])) {
                $key = substr($key, 0, strlen($key) - 1);
            } elseif (strpos($key, "data_") === 0) {
                $key = preg_replace('/_/', '-', $key, 1);
            }
            if ($value === true) {
                $params[] = $key;
            } elseif ($value === false) {
                continue;
            } else {
                $params[] = $key . '="' . e($value) . '"';
            }
        }
        return implode(" ", $params);
    }
}

if(!function_exists('chain')){
    /**
     * An implementation of Python's itertools.chain method.
     *
     * Makes an iterator that returns elements from teh first iterable until
     * it is exhausted, then proceeds to the next iterable, until all of the
     * iterables are exhausted. Used for treating consecutive sequences as a
     * single sequence
     *
     * @see https://docs.python.org/2/library/itertools.html#itertools.chain
     * @param array $iterables An array of iterables to iterate through
     * @return Generator
     */
    function chain(...$iterables){
        foreach($iterables as $it){
            foreach($it as $element){
                yield $element;
            }
        }
    }
}