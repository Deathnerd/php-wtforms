<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 11:15 AM
 */

namespace WTForms\Validators;

use WTForms\Exceptions\NotImplemented;
use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Validators\Options\ArrayOptions;

/**
 * @template TOptions of array<string, mixed>
 */
abstract class Validator
{
  /**
   * @var string Error message to raise in case of a validation error
   */
  public string $message;
  /**
   * @var array
   */
  public array $field_flags = [];
  /**
   * @var TOptions $options
   */
  private array $options;

  /**
   * @param string $message
   * @param TOptions|ArrayOptions<TOptions> $options
   */
  public function __construct(string $message = "", $options = [])
  {
    if ($options instanceof ArrayOptions) {
      $options = $options->toArray();
    }
    $this->message = $message;
    $this->options = $options;
  }

  /**
   * @param Form   $form
   * @param Field  $field
   * @param string $message
   */
  public abstract function __invoke(Form $form, Field $field, string $message = "");

  public function getOptions(): array
  {
    return $this->options;
  }

  /**
   * @return mixed|null
   */
  public function getOption(string $key)
  {
    return $this->options[$key] ?? null;
  }

  public function hasOption(string $key): bool
  {
    return array_key_exists($key, $this->options);
  }

  public function setOption(string $key, $value): void
  {
    $this->options[$key] = $value;
  }

  public function removeOption(string $key): void
  {
    unset($this->options[$key]);
  }

  public function setOptions(array $options): void
  {
    $this->options = $options;
  }

  public function getFieldFlags(): array
  {
    return $this->field_flags;
  }

  public function setFieldFlags(array $field_flags): void
  {
    $this->field_flags = $field_flags;
  }

  public function getMessage(): string
  {
    return $this->message;
  }

  public function setMessage(string $message): void
  {
    $this->message = $message;
  }
}

