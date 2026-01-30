<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/21/2016
 * Time: 11:15 AM.
 */

namespace WTForms\Validators;

use WTForms\Fields\Core\Field;
use WTForms\Form;
use WTForms\Internal\Types\ArrayOptions;

/**
 * @template TOptions of array<string, mixed>
 */
abstract class Validator
{
  /**
   * @var string Error message to raise in case of a validation error
   */
  public string $message;
  public array $field_flags = [];
  /**
   * @var TOptions
   */
  private array $options;

  /**
   * @param TOptions|ArrayOptions<TOptions> $options
   */
  /**
   * @deprecated Passing an array for $options is deprecated. Use a typed Options instance instead.
   */
  public function __construct(string $message = '', $options = [])
  {
    if ($options instanceof ArrayOptions) {
      $this->constructFromOptions($message, $options);
      return;
    }
    /** @deprecated Array path - use typed Options instead */
    $this->message = $message;
    $this->options = $options;
  }

  /**
   * Construct from a typed Options object.
   */
  protected function constructFromOptions(string $message, ArrayOptions $options): void
  {
    $this->message = $message;
    $this->options = $options->toArray();
    $options->validate();
    $options->hydrateObject($this);
    $this->postHydrate();
  }

  /**
   * Hook for subclasses to run post-construction derived state.
   */
  protected function postHydrate(): void
  {
  }

  abstract public function __invoke(Form $form, Field $field, string $message = '');

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
