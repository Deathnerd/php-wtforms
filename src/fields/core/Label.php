<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/20/2016
 * Time: 3:07 PM
 */

namespace WTForms\Fields\Core;


class Label
{
    /**
     * @var string The id of the field this label is for. Will be
     *             rendered in the `for` attribute for the label
     */
    public $field_id = "";
    /**
     * @var string The text for the label
     */
    public $text = "";

    /**
     * Label constructor.
     *
     * @param string $field_id
     * @param string $text
     */
    public function __construct($field_id, $text)
    {
        $this->field_id = $field_id;
        $this->text = $text;
    }

    public function __toString()
    {
        return (string)$this->__invoke();
    }

    /**
     * @param array $options The options to pass to the tag when rendering
     *
     * @return string The rendered label
     */
    public function __invoke(array $options = [])
    {
        $options = array_merge(["for" => $this->field_id, "text" => $this->text], $options);
        $text = $options['text'];
        unset($options['text']);

        return sprintf("<label %s>%s</label>", html_params($options), $text);
    }

    /**
     * @internal
     *
     * @param $name
     *
     * @return string
     */
    public function __get($name)
    {
        return $name == "for" ? $this->field_id : $this->$name;
    }
}
