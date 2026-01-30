<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

/**
 * Represents an HTML label element for a form field.
 */
class Label
{
  /**
   * The id of the field this label is for. Will be
   * rendered in the `for` attribute for the label.
   */
  public string $field_id = '';

  /**
   * The text for the label.
   */
  public string $text = '';

  /**
   * Label constructor.
   */
  public function __construct(string $field_id, string $text)
  {
    $this->field_id = $field_id;
    $this->text = $text;
  }

  public function __toString(): string
  {
    return $this->__invoke();
  }

  /**
   * Render the label as HTML.
   *
   * @param array<string, mixed> $options The options to pass to the tag when rendering
   *
   * @return string The rendered label
   */
  public function __invoke(array $options = []): string
  {
    $options = array_merge(['for' => $this->field_id, 'text' => $this->text], $options);
    $text = $options['text'];
    unset($options['text']);

    return sprintf('<label %s>%s</label>', html_params($options), $text);
  }

  /**
   * @internal
   *
   * @return string
   */
  public function __get(string $name)
  {
    return 'for' == $name ? $this->field_id : $this->$name;
  }
}
