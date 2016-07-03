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


//if (!function_exists('messages_path')) {
//  /**
//   * Determine the path to the 'messages' directory as best as possible
//   *
//   * @return string The best guess for where the messages are
//   */
//  function messages_path()
//  {
//    $module_path = __DIR__ . DS . "locale";
//    if (!file_exists($module_path)) {
//      $module_path = "/usr/share/locale";
//    }
//
//    return $module_path;
//  }
//}

//if (!function_exists('get_builtin_gnu_translations')) {
//  /**
//   * Returns the built-in GNU translations located in php-wtform's
//   * src root (src/locale/[languages here]
//   *
//   * @param string $language Which language to get
//   *
//   * @return \Gettext\GettextTranslator The resulting translation object
//   */
//  function get_builtin_gnu_translations($language = "")
//  {
//    if (!$language) {
//      $language = "en_US";
//    }
//    $domain = messages_path();
//    $t = new \Gettext\GettextTranslator();
//    $t->setLanguage($language);
//    $t->loadDomain('wtforms', $domain);
//
//    return $t;
//  }
//}

//if (!function_exists('get_translations')) {
//  /**
//   * Gets a translation object
//   *
//   * @param null|string   $languages
//   * @param null|callable $getter
//   *
//   * @return \Gettext\GettextTranslator
//   */
//  function get_translations($languages = null, $getter = null)
//  {
//    if (is_callable($getter)) {
//      return $getter($languages);
//    }
//
//    return get_builtin_gnu_translations($languages);
//  }
//}

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
     * @param array $options An associative array of attributes for an HTML tag
     *
     * @return string The string of HTML attributes
     */
    function html_params($options)
    {
        $params = [];
        ksort($options);
        foreach ($options as $key => $value) {
            if (in_array($key, ['class_', 'class__', 'for_'])) {
                $key = str_replace("_", "", $key);
            } elseif (starts_with($key, "data_")) {
                $key = preg_replace('/_/', '-', $key);
            }
            if ($value === true) {
                $params[] = $key;
            } elseif ($value === false) {
                continue;
            } else {
                if (is_array($value)) {
                    $value = implode(" ", $value);
                }
                $params[] = $key . '="' . e($value) . '"';
            }
        }

        return implode(" ", $params);
    }
}

if (!function_exists('str_contains')) {
    /**
     * Convenience method for determining if a needle string is in a haystack string
     *
     * @param string $haystack The string to search in
     * @param string $needle   The string to search for
     *
     * @return bool
     */
    function str_contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('starts_with')) {
    /**
     * Convenience method for determining if a string starts with another string
     * or collection of strings
     *
     * @param string          $haystack The string to search in
     * @param string|string[] $needle   The string(s) to search for in the haystack
     *
     * @codeCoverageIgnore
     * @return bool
     */
    function starts_with($haystack, $needle)
    {
        if (is_array($needle)) {
            $ret = false;
            foreach ($needle as $n) {
                $ret |= starts_with($haystack, $n);
            }

            return $ret;
        }

        return strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('vsprintf_named')) {
    /**
     * A helper function to emulate Python's support of named tokens in format strings. Much thanks to user Jon at
     * StackOverflow for this function.
     *
     * Note: This function does not allow mixed non-named and named format tokens as this is meant to be used internally
     * with PHP-WTForms and exposed indirectly only by a couple of validators.
     *
     * @see http://stackoverflow.com/questions/7435233/name-php-specifiers-in-printf-strings
     * @see https://docs.python.org/2/library/stdtypes.html#string-formatting
     *
     * @param string $format The named format string to parse. These are in the style of Python's named string
     *                       formatting things, ie: "%(foo)s walks with %(bar)s" % {'foo': 'Sally', 'bar': 'Rick'}
     *                       returns a string of "Sally walks with Rick".
     *
     * @param  array $args   An associative array of values to map to the specified keys in the format string.
     *                       For example, ["foo" => "Sally", "bar" => 5]
     *
     * @return string The result of formatting the string with the given named parameters
     */
    function vsprintf_named($format, $args)
    {
        preg_match_all('/%\((.*?)\)/', $format, $matches, PREG_SET_ORDER);
        $values = array();
        foreach ($matches as $match) {
            $values[] = $args[$match[1]];
        }
        $format = preg_replace('/%\((.*?)\)/', '%', $format);

        return vsprintf($format, $values);
    }
}
