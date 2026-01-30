<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

use Generator;
use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValueError;
use WTForms\Fields\Options\SelectFieldOptions;
use WTForms\Form;
use WTForms\Widgets\Core\Select;

/**
 * Select field with coercible values.
 *
 * @phpstan-import-type SelectFieldOptionsArray from SelectFieldOptions
 *
 * @extends SelectFieldBase<string|int|null>
 */
class SelectField extends SelectFieldBase
{
  /**
   * Available choices as array of [value, label] tuples.
   *
   * @var array<int, array{0: string|int, 1: string}>
   */
  public array $choices = [];

  /**
   * Callable to coerce values to the appropriate type.
   *
   * @var callable
   */
  public $coerce;

  /**
   * @phpstan-param SelectFieldOptionsArray|SelectFieldOptions $options
   */
  public function __construct(array|SelectFieldOptions $options = ['choices' => []])
  {
    if ($options instanceof SelectFieldOptions) {
      $options->hydrateObject($this);
      parent::__construct($options);
      return;
    }

    if (array_key_exists('choices', $options)) {
      $this->choices = $options['choices'];
      unset($options['choices']);
    } else {
      $this->choices = [];
    }
    if (array_key_exists('coerce', $options)) {
      $this->coerce = $options['coerce'];
      unset($options['coerce']);
    } else {
      $this->coerce = function ($x) {
        return $x;
      };
    }
    $options = array_merge(['widget' => new Select()], $options);
    parent::__construct($options);
  }

  protected function postHydrate(): void
  {
    if (!$this->widget) {
      $this->widget = new Select();
    }
    if (!isset($this->coerce)) {
      $this->coerce = fn($x) => $x;
    }
  }

  /**
   * @return Generator<int, array{0: string|int, 1: string, 2: bool}>
   */
  public function getChoices(): Generator
  {
    foreach ($this->choices as list($value, $label)) {
      yield [$value, $label, $value == $this->data];
    }
  }

  /**
   * @param string|int|null $value
   */
  public function processData($value): void
  {
    try {
      $this->data = ($this->coerce)($value);
    } catch (ValueError $e) {
      $this->data = null;
    } catch (TypeError $e) {
      $this->data = null;
    }
  }

  /**
   * @param array<int, string> $valuelist
   *
   * @throws ValueError
   */
  public function processFormData(array $valuelist): void
  {
    if ($valuelist) {
      try {
        $this->data = ($this->coerce)($valuelist[0]);
      } catch (ValueError $e) {
        throw new ValueError('Invalid choice: could not coerce');
      }
    }
  }

  /**
   * @throws ValueError
   */
  public function preValidate(Form $form): void
  {
    foreach ($this->choices as list($v, $_)) {
      if ($this->data == $v) {
        return;
      }
    }

    throw new ValueError('Not a valid choice');
  }
}
