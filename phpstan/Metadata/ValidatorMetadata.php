<?php

declare(strict_types=1);

namespace WTForms\PHPStan\Metadata;

use Throwable;
use WTForms\Exceptions\StopValidation;
use WTForms\Exceptions\ValidationError;
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

/**
 * Central registry for validator metadata.
 *
 * Maps validators to their exception types and type narrowing rules.
 * This metadata is used by PHPStan extensions to provide accurate
 * static analysis of the validation system.
 */
final class ValidatorMetadata
{
  /**
   * Maps validator classes to the exception types they throw.
   *
   * Validators that halt validation chain throw StopValidation.
   * Validators that report field errors throw ValidationError.
   *
   * @var array<class-string, class-string<Throwable>>
   */
  public const THROW_TYPES = [
    // Stop validators - halt the validation chain
    DataRequired::class  => StopValidation::class,
    InputRequired::class => StopValidation::class,
    Optional::class      => StopValidation::class,
    RequiredIf::class    => StopValidation::class,

    // Error validators - report field validation errors
    Length::class      => ValidationError::class,
    Email::class       => ValidationError::class,
    URL::class         => ValidationError::class,
    IPAddress::class   => ValidationError::class,
    MacAddress::class  => ValidationError::class,
    UUID::class        => ValidationError::class,
    NumberRange::class => ValidationError::class,
    Regexp::class      => ValidationError::class,
    AnyOf::class       => ValidationError::class,
    NoneOf::class      => ValidationError::class,
    EqualTo::class     => ValidationError::class,
  ];

  /**
   * Type narrowing rules applied after a validator passes.
   *
   * - 'removes': Types that are eliminated from the union (e.g., null)
   * - 'narrows_to': The resulting type after validation passes
   *
   * @var array<class-string, array{removes?: list<string>, narrows_to?: string}>
   */
  public const TYPE_NARROWING = [
    DataRequired::class  => ['removes' => ['null']],
    InputRequired::class => ['removes' => ['null']],
    Email::class         => ['removes' => ['null'], 'narrows_to' => 'non-empty-string'],
    URL::class           => ['removes' => ['null'], 'narrows_to' => 'non-empty-string'],
    IPAddress::class     => ['removes' => ['null'], 'narrows_to' => 'non-empty-string'],
    MacAddress::class    => ['removes' => ['null'], 'narrows_to' => 'non-empty-string'],
    UUID::class          => ['removes' => ['null'], 'narrows_to' => 'non-empty-string'],
    NumberRange::class   => ['removes' => ['null'], 'narrows_to' => 'int|float'],
    Regexp::class        => ['removes' => ['null'], 'narrows_to' => 'string'],
  ];
}
