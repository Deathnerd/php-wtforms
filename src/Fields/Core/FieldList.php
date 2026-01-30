<?php

declare(strict_types=1);

namespace WTForms\Fields\Core;

use ArrayAccess;
use Countable;
use stdClass;
use WTForms\Exceptions\AssertionError;
use WTForms\Exceptions\IndexError;
use WTForms\Exceptions\TypeError;
use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Options\FieldListOptions;
use WTForms\Form;
use WTForms\Validators\Validator;
use WTForms\Widgets\Core\ListWidget;

/**
 * Encapsulate an ordered list of multiple instances of the same field type,
 * keeping data as a list.
 *
 * >>> $authors = new FieldList(new StringField("name", [new DataRequired()]));
 *
 * @phpstan-import-type FieldListOptionsArray from FieldListOptions
 * @phpstan-type FieldListDataType array<int, mixed>
 *
 * @extends Field<FieldListDataType>
 */
class FieldList extends Field implements Countable, ArrayAccess
{
  /**
   * Minimum number of entries to display.
   */
  public int $min_entries = 0;

  /**
   * Used internally as an upper bound for how many subfields
   * the field list is allowed to generate.
   * !!!! WARNING !!!!
   * This should be manipulated only via the constructor as
   * there are assertion checks to make sure this is always
   * greater than or equal to {@link min_entries}. Undefined
   * and untested behavior may occur if this is manipulated
   * otherwise. This is kept public purely for testing purposes
   * only.
   */
  public ?int $max_entries = null;

  /**
   * The field class that will be used to generate subfields.
   *
   * @var Field<mixed>
   */
  public Field $inner_field;

  /**
   * Used internally to keep track of the last index generated
   * for the subfields. Also used for testing purposes. This
   * should not be used for any other purposes.
   */
  public int $last_index = -1;

  /**
   * FieldList constructor.
   *
   * @phpstan-param FieldListOptionsArray|FieldListOptions $options
   *
   * @throws TypeError
   */
  public function __construct(array|FieldListOptions $options = new FieldListOptions())
  {
    if (is_array($options)) {
      $options = new FieldListOptions($options);
    }
    $options->hydrateObject($this);
    parent::__construct($options);
  }

  protected function postHydrate(): void
  {
    if (!$this->widget) {
      $this->widget = new ListWidget();
    }
  }

  /**
   * Process incoming data, calling process_data, process_formdata as needed,
   * and run filters.
   *
   * If `data` is not provided, process_data will be called on the field's
   * default.
   *
   * Field subclasses usually won't override this, instead overriding the
   * process_formdata and process_data methods. Only override this for
   * special advanced processing, such as when a field encapsulates many
   * inputs.
   *
   * @param array<string, mixed>   $formdata
   * @param array<int, mixed>|null $data
   * @throws AssertionError
   */
  public function process(array $formdata, $data = null): void
  {
    // @codeCoverageIgnoreStart
    $this->entries = [];
    if (!$data) {
      if (is_callable($this->default)) {
        $data = ($this->default)();
      } else {
        $data = $this->default;
      }
    }
    // @codeCoverageIgnoreEnd

    $this->object_data = $data;

    if ($formdata) {
      $indices = array_unique($this->extractIndices($this->name, $formdata));
      sort($indices);
      if ($this->max_entries) {
        $indices = array_slice($indices, 0, $this->max_entries);
      }

      $data_length = 0;
      // First add formdata that map to indices
      foreach ($indices as $index) {
        if (!is_array($data) || $data_length >= count($data)) {
          $obj_data = null;
        } else {
          $obj_data = $data[$data_length];
        }
        $this->addEntry($formdata, $obj_data, $index);
        ++$data_length;
      }
    } else {
      // Or add data that maps to object data
      if (is_array($data)) {
        foreach ($data as $obj_data) {
          $this->addEntry($formdata, $obj_data);
        }
      }
    }
    // Pad out the entries until there is enough data
    // to hit the minimum entries limit
    while (count($this->entries) < $this->min_entries) {
      $this->addEntry($formdata);
    }
  }

  /**
   * Yield indices of any keys with given prefix. Keys are in the format
   * of: prefix-numeric_index-subfield_name.
   *
   * $formdata must be an object which will produce keys when iterated.
   *
   * @param array<string, mixed> $formdata
   *
   * @return array<int, int|string>
   */
  private function extractIndices(string $prefix, array $formdata): array
  {
    $offset = strlen($prefix) + 1;
    $ret = [];
    // Array keys are in the form of "prefix-numeric_index-subfield_name"
    foreach (array_keys($formdata) as $k) {
      if (str_contains($k, $prefix)) {
        $k = explode('-', substr($k, $offset), 2)[0];
        if (is_numeric($k)) {
          $ret[] = $k;
        }
      }
    }

    return $ret;
  }

  /**
   * Processes an unbound field and inserts it as a field type in this field list.
   *
   * @return Field<mixed>
   *
   * @throws AssertionError
   */
  private function addEntry(array $formdata = [], $data = null, int|string|null $index = null): Field
  {
    if (!(!$this->max_entries || count($this->entries) < $this->max_entries)) {
      throw new AssertionError('You cannot have more than max_entries entries in the FieldList');
    }

    if (null === $index) {
      $index = $this->last_index + 1;
    }
    $this->last_index = (int)$index;
    $name = "$this->short_name-$index";
    $id = "$this->id-$index";
    // I feel so dirty... There's gotta be a better way - Deathnerd
    $inner_field_options = $this->inner_field->constructor_options;
    $inner_field_options['meta'] = $this->meta;
    $inner_field_options['prefix'] = $this->prefix;
    $inner_field_options['name'] = $name;
    $inner_field_options['id'] = $id;
    if ($this->inner_field instanceof FormField) {
      $inner_field_options['form_class'] = $this->inner_field->form_class;
      unset($inner_field_options['filters']);
      unset($inner_field_options['validators']);
    }

    /**
     * @var Field<mixed> $field
     */
    $field = new $this->inner_field($inner_field_options);
    if (!$field->label instanceof Label) {
      if (is_string($field->label) && $field->label !== '') {
        $text = $field->label;
      } else {
        $text = ucwords(str_replace('_', ' ', $field->short_name));
      }
      $field->label = new Label($field->id, $text);
    }
    $field->process($formdata, $data);

    $this->entries[] = $field;

    return $field;
  }

  /**
   * Create a new entry with optional default data.
   *
   * Entries added in this way will *not* receive formdata however, and can
   * only receive object data.
   *
   * @return Field<mixed>
   * @throws AssertionError
   */
  public function appendEntry($data = null): Field
  {
    return $this->addEntry([], $data);
  }

  /**
   * Removes the last entry from the list and returns it.
   *
   * @return Field<mixed>
   *
   * @throws IndexError
   */
  public function popEntry(): Field
  {
    if (0 == count($this->entries)) {
      throw new IndexError();
    }
    $entry = array_pop($this->entries);
    --$this->last_index;

    return $entry;
  }

  /**
   * Validate this FieldList.
   *
   * Note that the FieldList differs from normal field validation in
   * that FieldList validates all its enclosed fields first before running any
   * of its own validators.
   * @inheritdoc
   */
  public function validate(Form $form, Validator|callable ...$extra_validators): bool
  {
    $this->errors = [];

    // Run validators on all entries within
    foreach ($this->entries as $subfield) {
      /**
       * @var Field<mixed> $subfield
       */
      if (!$subfield->validate($form)) {
        $this->errors[] = $subfield->errors;
      }
    }
    $this->runValidationChain($form, array_merge($this->validators, $extra_validators));

    return 0 == count($this->errors);
  }

  public function populateObj(object $obj, string $name): void
  {
    $values = $obj->$name;
    if (!is_array($values)) {
      $values = [];
    }
    $output = [];
    $i = 0;
    foreach ($this->entries as $field) {
      /**
       * @var Field<mixed> $field
       */
      $fake_obj = new stdClass();
      $fake_obj->data = @$values[$i];
      $field->populateObj($fake_obj, 'data');
      $output[] = $fake_obj->data;
      ++$i;
    }
    $obj->$name = $output;
  }

  /**
   * @return array<int, mixed>|mixed
   */
  public function __get(string $name)
  {
    if ('data' == $name) {
      $ret = [];
      foreach ($this->entries as $entry) {
        $ret[] = $entry->data;
      }

      return $ret;
    }

    return parent::__get($name); // @codeCoverageIgnore
  }

  public function count(): int
  {
    return count($this->entries);
  }

  /**
   * @param int|string $offset
   *
   * @codeCoverageIgnore
   */
  public function offsetExists($offset): bool
  {
    return isset($this->entries[$offset]);
  }

  /**
   * @param int|string $offset
   *
   * @return Field<mixed>|null
   *
   * @codeCoverageIgnore
   */

  public function offsetGet($offset): ?Field
  {
    if (isset($this->entries[$offset])) {
      return $this->entries[$offset];
    }

    return null;
  }

  /**
   * @param int|string|null $offset
   * @param Field<mixed>    $value
   *
   * @codeCoverageIgnore
   */
  public function offsetSet($offset, $value): void
  {
    if (is_null($offset)) {
      $this->entries[] = $value;
    } else {
      $this->entries[$offset] = $value;
    }
  }

  /**
   * @param int|string $offset
   *
   * @codeCoverageIgnore
   */
  public function offsetUnset($offset): void
  {
    unset($this->entries[$offset]);
  }
}
