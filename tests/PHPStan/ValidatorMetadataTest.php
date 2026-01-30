<?php

declare(strict_types=1);

namespace WTForms\Tests\PHPStan;

use PHPUnit\Framework\TestCase;
use Throwable;
use WTForms\Exceptions\StopValidation;
use WTForms\Exceptions\ValidationError;
use WTForms\PHPStan\Metadata\ValidatorMetadata;
use WTForms\Validators\AnyOf;
use WTForms\Validators\DataRequired;
use WTForms\Validators\Email;
use WTForms\Validators\EqualTo;
use WTForms\Validators\InputRequired;
use WTForms\Validators\IPAddress;
use WTForms\Validators\Length;
use WTForms\Validators\MacAddress;
use WTForms\Validators\NoneOf;
use WTForms\Validators\NumberRange;
use WTForms\Validators\Optional;
use WTForms\Validators\Regexp;
use WTForms\Validators\RequiredIf;
use WTForms\Validators\URL;
use WTForms\Validators\UUID;
use WTForms\Validators\Validator;

class ValidatorMetadataTest extends TestCase
{
  public function testThrowTypesContainsAllExpectedValidators(): void
  {
    $expectedValidators = [
      DataRequired::class,
      InputRequired::class,
      Optional::class,
      RequiredIf::class,
      Length::class,
      Email::class,
      URL::class,
      IPAddress::class,
      MacAddress::class,
      UUID::class,
      NumberRange::class,
      Regexp::class,
      AnyOf::class,
      NoneOf::class,
      EqualTo::class,
    ];

    foreach ($expectedValidators as $validator) {
      $this->assertArrayHasKey(
        $validator,
        ValidatorMetadata::THROW_TYPES,
        "Missing throw type for {$validator}"
      );
    }
  }

  public function testStopValidatorsThrowStopValidation(): void
  {
    $stopValidators = [
      DataRequired::class,
      InputRequired::class,
      Optional::class,
      RequiredIf::class,
    ];

    foreach ($stopValidators as $validator) {
      $this->assertEquals(
        StopValidation::class,
        ValidatorMetadata::THROW_TYPES[$validator],
        "{$validator} should throw StopValidation"
      );
    }
  }

  public function testErrorValidatorsThrowValidationError(): void
  {
    $errorValidators = [
      Length::class,
      Email::class,
      URL::class,
      IPAddress::class,
      MacAddress::class,
      UUID::class,
      NumberRange::class,
      Regexp::class,
      AnyOf::class,
      NoneOf::class,
      EqualTo::class,
    ];

    foreach ($errorValidators as $validator) {
      $this->assertEquals(
        ValidationError::class,
        ValidatorMetadata::THROW_TYPES[$validator],
        "{$validator} should throw ValidationError"
      );
    }
  }

  public function testAllThrowTypesAreValidExceptionClasses(): void
  {
    foreach (ValidatorMetadata::THROW_TYPES as $validator => $exceptionClass) {
      $this->assertTrue(
        class_exists($validator),
        "Validator class {$validator} does not exist"
      );
      $this->assertTrue(
        is_subclass_of($validator, Validator::class),
        "{$validator} is not a Validator subclass"
      );
      $this->assertTrue(
        class_exists($exceptionClass),
        "Exception class {$exceptionClass} does not exist"
      );
      $this->assertTrue(
        is_subclass_of($exceptionClass, Throwable::class),
        "{$exceptionClass} is not Throwable"
      );
    }
  }

  public function testTypeNarrowingStructure(): void
  {
    foreach (ValidatorMetadata::TYPE_NARROWING as $validator => $rules) {
      $this->assertTrue(
        class_exists($validator),
        "Validator class {$validator} does not exist"
      );

      // Each entry should have 'removes' and/or 'narrows_to'
      $this->assertTrue(
        isset($rules['removes']) || isset($rules['narrows_to']),
        "{$validator} narrowing rules must have 'removes' or 'narrows_to'"
      );

      if (isset($rules['removes'])) {
        $this->assertIsArray($rules['removes']);
      }

      if (isset($rules['narrows_to'])) {
        $this->assertIsString($rules['narrows_to']);
      }
    }
  }

  public function testRequiredValidatorsRemoveNull(): void
  {
    $nullRemovingValidators = [
      DataRequired::class,
      InputRequired::class,
    ];

    foreach ($nullRemovingValidators as $validator) {
      $this->assertArrayHasKey(
        $validator,
        ValidatorMetadata::TYPE_NARROWING,
        "{$validator} should have type narrowing rules"
      );
      $this->assertContains(
        'null',
        ValidatorMetadata::TYPE_NARROWING[$validator]['removes'] ?? [],
        "{$validator} should remove null type"
      );
    }
  }

  public function testStringValidatorsNarrowToNonEmptyString(): void
  {
    $stringValidators = [
      Email::class,
      URL::class,
      IPAddress::class,
      MacAddress::class,
      UUID::class,
    ];

    foreach ($stringValidators as $validator) {
      $this->assertArrayHasKey(
        $validator,
        ValidatorMetadata::TYPE_NARROWING,
        "{$validator} should have type narrowing rules"
      );
      $this->assertEquals(
        'non-empty-string',
        ValidatorMetadata::TYPE_NARROWING[$validator]['narrows_to'] ?? null,
        "{$validator} should narrow to non-empty-string"
      );
    }
  }

  public function testNumberRangeNarrowsToNumeric(): void
  {
    $this->assertArrayHasKey(NumberRange::class, ValidatorMetadata::TYPE_NARROWING);
    $this->assertEquals(
      'int|float',
      ValidatorMetadata::TYPE_NARROWING[NumberRange::class]['narrows_to']
    );
  }

  public function testRegexpNarrowsToString(): void
  {
    $this->assertArrayHasKey(Regexp::class, ValidatorMetadata::TYPE_NARROWING);
    $this->assertEquals(
      'string',
      ValidatorMetadata::TYPE_NARROWING[Regexp::class]['narrows_to']
    );
  }
}
