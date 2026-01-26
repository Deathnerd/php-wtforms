<?php

declare(strict_types=1);

namespace WTForms\Widgets\Core;

use WTForms\Fields\Core\Field;

/**
 * Render a single radio button.
 *
 * This widget is most commonly used in conjunction with ListWidget or some
 * other listing, as singular radio buttons are not very useful.
 *
 * @extends Input<string|int>
 */
class RadioInput extends Input
{
  public function __construct()
  {
    parent::__construct('radio');
  }

  public function __invoke(Field $field, array $options = []): string
  {
    if ($field->checked) {
      $options['checked'] = true;
    }

    return parent::__invoke($field, $options);
  }
}
