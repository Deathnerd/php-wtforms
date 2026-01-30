<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

use ArrayAccess;
use WTForms\Exceptions\TypeError;
use WTForms\Fields\Options\FieldOptions;
use WTForms\Form;
use WTForms\Validators\Validator;
use WTForms\Widgets\Core\TableWidget;

/**
 * Encapsulate a form as a field into another form.
 *
 * @phpstan-import-type FieldOptionsArray from FieldOptions
 * @phpstan-type FormFieldOptionsArray array{ form_class?: class-string<Form> }
 * @phpstan-type FormFieldDataType array<string,mixed>
 * @extends Field<FormFieldDataType>
 */
class FormField extends Field implements ArrayAccess
{
  /**
   * Separator between form prefix and field name.
   */
  public string $separator = '-';

  /**
   * The class name of the form to embed, or an instance of the form.
   */
  public Form|string $form_class;

  private bool $usedDefault = false;

  /**
   * @phpstan-param FormFieldOptionsArray & FieldOptionsArray $options
   *
   * @throws TypeError
   */
  public function __construct(array $options = [])
  {
    if (!array_key_exists('form_class', $options)) {
      throw new TypeError('FormField must have a form_class property set!');
    }
    if (array_key_exists('filters', $options)) {
      throw new TypeError('FormField cannot take filters, as the encapsulated data is not mutable');
    }

    if (array_key_exists('validators', $options)) {
      throw new TypeError('FormField does not accept any validators. Instead, define them on the enclosed form.');
    }

    $this->form_class = $options['form_class'];
    unset($options['form_class']);
    if (array_key_exists('separator', $options)) {
      $this->separator = $options['separator'];
      unset($options['separator']);
    }
    $options = array_merge(['widget' => new TableWidget()], $options);
    parent::__construct($options);
  }

  /**
   * @param FormFieldDataType             $formdata
   * @param object|FormFieldDataType|null $data
   */
  public function process(array $formdata, $data = null): void
  {
    if (!$data) {
      if (is_callable($this->default)) {
        $data = ($this->default)();
      } else {
        $data = $this->default;
      }
      $this->usedDefault = true;
    }

    $this->object_data = $data;
    $prefix = $this->name.$this->separator;

    /**
     * @var class-string<Form> $form_class
     */
    $form_class = $this->form_class;
    if (is_array($data)) {
      $this->form = $form_class::create([
        'prefix'   => $prefix,
        'formdata' => $formdata,
        'data'     => $data,
      ]);
    } else {
      $this->form = $form_class::create([
        'prefix'   => $prefix,
        'formdata' => $formdata,
        'obj'      => $data,
      ]);
    }
  }

  /**
   * @inheritdoc
   * @throws TypeError
   */
  public function validate(Form $form, Validator|callable ...$extra_validators): bool
  {
    if ($extra_validators) {
      throw new TypeError('FormField does not accept in-line validators, as it gets errors from the enclosed form.');
    }

    return $this->form->validate();
  }

  public function populateObj(object $obj, string $name): void
  {
    $candidate = $obj->$name;
    if (is_null($candidate)) {
      if (!$this->usedDefault) {
        throw new TypeError('populate_obj: cannot find a value to populate from the provided obj or input data/defaults');
      }
      $candidate = $this->object_data;
      $obj->$name = $candidate;
    }
    $this->form->populateObj($candidate);
  }

  public function __get(string $name)
  {
    if (in_array($name, ['data', 'errors']) || property_exists($this->form, $name)) {
      return $this->form->$name;
    }
    if (array_key_exists($name, $this->form->fields)) {
      return $this->form[$name];
    }

    return parent::__get($name); // @codeCoverageIgnore
  }

  /**
   * @return Field<FormFieldDataType>|false
   *
   * @codeCoverageIgnore
   */

  public function current(): Field|false
  {
    return current($this->form->fields);
  }

  /**
   * @codeCoverageIgnore
   */
  public function next(): void
  {
    next($this->form->fields);
  }

  /**
   * @codeCoverageIgnore
   */
  public function key() : int|string|null
  {
    return key($this->form->fields);
  }

  /**
   * @codeCoverageIgnore
   */
  public function valid(): bool
  {
    $key = key($this->form->fields);

    return null !== $key;
  }

  /**
   * @codeCoverageIgnore
   */
  public function rewind(): void
  {
    reset($this->form->fields);
  }

  /**
   * @param string|int $offset
   *
   * @codeCoverageIgnore
   */
  public function offsetExists($offset): bool
  {
    return array_key_exists($offset, $this->form->fields);
  }

  /**
   * @param string|int $offset
   *
   * @return Field<mixed>|null
   *
   * @codeCoverageIgnore
   */
  public function offsetGet($offset): ?Field
  {
    if (array_key_exists($offset, $this->form->fields)) {
      return $this->form->fields[$offset];
    }

    return null;
  }

  /**
   * @param string|int|null $offset
   * @param Field<mixed>    $value
   *
   * @codeCoverageIgnore
   */
  public function offsetSet($offset, $value): void
  {
    $this->form->fields[$offset] = $value;
    $this->form->$offset = $value;
  }

  /**
   * @param string|int $offset
   *
   * @codeCoverageIgnore
   */
  public function offsetUnset($offset): void
  {
    unset($this->form->fields[$offset]);
    $this->form->$offset = null;
  }
}
