<?php

/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/27/2016
 * Time: 11:21 PM.
 */

namespace WTForms\Validators;

use WTForms\Exceptions\ValidationError;
use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Internal\Types\ArrayOptions;
use WTForms\Validators\Options\UUIDOptions;

/**
 * Validates a UUID.
 *
 * @phpstan-import-type UUIDOptionsArray from UUIDOptions
 */
class UUID extends Regexp
{
  /**
   * UUID constructor.
   *
   * @param string                       $message Error message to raise in case of validation error
   * @param UUIDOptionsArray|UUIDOptions $options
   */
  public function __construct(string $message = '', array|UUIDOptions $options = new UUIDOptions())
  {
    if ($options instanceof UUIDOptions) {
      parent::__construct($message, $options);
      return;
    }
    if (!isset($options['regex'])) {
      $options['regex'] = '/^[0-9a-fA-F]{8}-([0-9a-fA-F]{4}-){3}[0-9a-fA-F]{12}$/';
    }
    parent::__construct($message, $options);
  }

  /**
   * @return string The matched UUID string
   *
   * @throws ValidationError
   *
   * @phpstan-assert non-empty-string $field->data
   */
  public function __invoke(Form $form, Field $field, string $message = ''): string
  {
    $message = $this->message;
    if (!$message) {
      $message = 'Invalid UUID.';
    }

    return parent::__invoke($form, $field, $message);
  }
}
