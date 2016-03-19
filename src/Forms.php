<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/14/2016
 * Time: 7:48 PM
 */

namespace WTForms;

use mindplay\annotations\Annotation;
use mindplay\annotations\AnnotationException;
use mindplay\annotations\Annotations;
use ReflectionProperty;
use WTForms\Fields\Core\Field;
use WTForms\Validators\Validator;
use WTForms\Widgets\Core\Widget;


class Forms
{
  private static $instance = null;

  private static $registeredValidators = [
      'WTForms\Validators\Annotations\AnyOfAnnotation'         => 'WTForms\Validators\AnyOf',
      'WTForms\Validators\Annotations\DataRequiredAnnotation'  => 'WTForms\Validators\DataRequired',
      'WTForms\Validators\Annotations\EqualToAnnotation'       => 'WTForms\Validators\EqualTo',
      'WTForms\Validators\Annotations\InputRequiredAnnotation' => 'WTForms\Validators\InputRequired',
      'WTForms\Validators\Annotations\IPAddressAnnotation'     => 'WTForms\Validators\IPAddress',
      'WTForms\Validators\Annotations\LengthAnnotation'        => 'WTForms\Validators\Length',
      'WTForms\Validators\Annotations\MacAddressAnnotation'    => 'WTForms\Validators\MacAddress',
      'WTForms\Validators\Annotations\NoneOfAnnotation'        => 'WTForms\Validators\NoneOf',
      'WTForms\Validators\Annotations\NumberRangeAnnotation'   => 'WTForms\Validators\NumberRange',
      'WTForms\Validators\Annotations\OptionalAnnotation'      => 'WTForms\Validators\Optional',
      'WTForms\Validators\Annotations\RegexpAnnotation'        => 'WTForms\Validators\Regexp',
      'WTForms\Validators\Annotations\URLAnnotation'           => 'WTForms\Validators\URL',
      'WTForms\Validators\Annotations\UUIDAnnotation'          => 'WTForms\Validators\UUID',
  ];
  private static $registeredFields = [
      'WTForms\Fields\Core\Annotations\BooleanFieldAnnotation'        => 'WTForms\Fields\Core\BooleanField',
      'WTForms\Fields\Core\Annotations\DateFieldAnnotation'           => 'WTForms\Fields\Core\DateField',
      'WTForms\Fields\Core\Annotations\DateTimeFieldAnnotation'       => 'WTForms\Fields\Core\DateTimeField',
      'WTForms\Fields\Core\Annotations\DecimalFieldAnnotation'        => 'WTForms\Fields\Core\DecimalField',
      'WTForms\Fields\Core\Annotations\FloatFieldAnnotation'          => 'WTForms\Fields\Core\FloatField',
      'WTForms\Fields\Core\Annotations\IntegerFieldAnnotation'        => 'WTForms\Fields\Core\IntegerField',
      'WTForms\Fields\Core\Annotations\RadioFieldAnnotation'          => 'WTForms\Fields\Core\RadioField',
      'WTForms\Fields\Core\Annotations\SelectFieldAnnotation'         => 'WTForms\Fields\Core\SelectField',
      'WTForms\Fields\Core\Annotations\SelectMultipleFieldAnnotation' => 'WTForms\Fields\Core\SelectMultipleField',
      'WTForms\Fields\Core\Annotations\StringFieldAnnotation'         => 'WTForms\Fields\Core\StringField',
      'WTForms\Fields\HTML5\Annotations\DateFieldAnnotation'          => 'WTForms\Fields\HTML5\DateField',
      'WTForms\Fields\HTML5\Annotations\DateTimeFieldAnnotation'      => 'WTForms\Fields\HTML5\DateTimeField',
      'WTForms\Fields\HTML5\Annotations\DateTimeLocalFieldAnnotation' => 'WTForms\Fields\HTML5\DateTimeLocalField',
      'WTForms\Fields\HTML5\Annotations\DecimalFieldAnnotation'       => 'WTForms\Fields\HTML5\DecimalField',
      'WTForms\Fields\HTML5\Annotations\DecimalRangeFieldAnnotation'  => 'WTForms\Fields\HTML5\DecimalRangeField',
      'WTForms\Fields\HTML5\Annotations\EmailFieldAnnotation'         => 'WTForms\Fields\HTML5\EmailField',
      'WTForms\Fields\HTML5\Annotations\IntegerFieldAnnotation'       => 'WTForms\Fields\HTML5\IntegerField',
      'WTForms\Fields\HTML5\Annotations\IntegerRangeFieldAnnotation'  => 'WTForms\Fields\HTML5\IntegerRangeField',
      'WTForms\Fields\HTML5\Annotations\SearchFieldAnnotation'        => 'WTForms\Fields\HTML5\SearchField',
      'WTForms\Fields\HTML5\Annotations\TelFieldAnnotation'           => 'WTForms\Fields\HTML5\TelField',
      'WTForms\Fields\HTML5\Annotations\URLFieldAnnotation'           => 'WTForms\Fields\HTML5\URLField',
      'WTForms\Fields\Simple\Annotations\FileFieldAnnotation'         => 'WTForms\Fields\Simple\FileField',
      'WTForms\Fields\Simple\Annotations\HiddenFieldAnnotation'       => 'WTForms\Fields\Simple\HiddenField',
      'WTForms\Fields\Simple\Annotations\PasswordFieldAnnotation'     => 'WTForms\Fields\Simple\PasswordField',
      'WTForms\Fields\Simple\Annotations\SubmitFieldAnnotation'       => 'WTForms\Fields\Simple\SubmitField',
      'WTForms\Fields\Simple\Annotations\TextAreaFieldAnnotation'     => 'WTForms\Fields\Simple\TextAreaField',
  ];

  private static $registeredWidgets = [
      'WTForms\Widgets\Core\Annotations\CheckboxInputAnnotation' => 'WTForms\Widgets\Core\CheckboxInput',
      'WTForms\Widgets\Core\Annotations\FileInputAnnotation'     => 'WTForms\Widgets\Core\FileInput',
      'WTForms\Widgets\Core\Annotations\HiddenInputAnnotation'   => 'WTForms\Widgets\Core\HiddenInput',
      'WTForms\Widgets\Core\Annotations\ListAnnotation'          => 'WTForms\Widgets\Core\List',
      'WTForms\Widgets\Core\Annotations\OptionAnnotation'        => 'WTForms\Widgets\Core\Option',
      'WTForms\Widgets\Core\Annotations\PasswordInputAnnotation' => 'WTForms\Widgets\Core\PasswordInput',
      'WTForms\Widgets\Core\Annotations\RadioInputAnnotation'    => 'WTForms\Widgets\Core\RadioInput',
      'WTForms\Widgets\Core\Annotations\SelectAnnotation'        => 'WTForms\Widgets\Core\Select',
      'WTForms\Widgets\Core\Annotations\SubmitInputAnnotation'   => 'WTForms\Widgets\Core\SubmitInput',
      'WTForms\Widgets\Core\Annotations\TableAnnotation'         => 'WTForms\Widgets\Core\Table',
      'WTForms\Widgets\Core\Annotations\TextAreaAnnotation'      => 'WTForms\Widgets\Core\TextArea',
      'WTForms\Widgets\Core\Annotations\TextInputAnnotation'     => 'WTForms\Widgets\Core\TextInput',
  ];

  private static $labelClass = 'WTForms\Fields\Label';

  /**
   * @return array
   */
  public static function getRegisteredValidators()
  {
    return self::$registeredValidators;
  }

  /**
   * @param array $registeredValidators
   */
  public static function setRegisteredValidators($registeredValidators)
  {
    self::$registeredValidators = array_merge($registeredValidators, self::$registeredValidators);
  }

  /**
   * @param string $annotation_namespace
   * @param string $validator_namespace
   */
  public static function registerValidator($annotation_namespace, $validator_namespace)
  {
    self::$registeredValidators[$annotation_namespace] = $validator_namespace;
  }

  /**
   * @return array
   */
  public static function getRegisteredFields()
  {
    return self::$registeredFields;
  }

  /**
   * @param array $registeredFields
   */
  public static function setRegisteredFields($registeredFields)
  {
    self::$registeredFields = array_merge($registeredFields, self::$registeredFields);
  }

  /**
   * @param string $annotation_namespace
   * @param string $field_namespace
   */
  public static function registerField($annotation_namespace, $field_namespace)
  {
    self::$registeredFields[$annotation_namespace] = $field_namespace;
  }

  /**
   * @return array
   */
  public static function getRegisteredWidgets()
  {
    return self::$registeredWidgets;
  }

  /**
   * @param array $registeredWidgets
   */
  public static function setRegisteredWidgets($registeredWidgets)
  {
    self::$registeredWidgets = array_merge($registeredWidgets, self::$registeredWidgets);
  }

  /**
   * @param string $annotation_namespace
   * @param string $widget_namespace
   */
  public static function registerWidget($annotation_namespace, $widget_namespace)
  {
    self::$registeredWidgets[$annotation_namespace] = $widget_namespace;
  }

  /**
   * @return string
   */
  public static function getLabelClass()
  {
    return self::$labelClass;
  }

  /**
   * @param string $labelClass
   */
  public static function setLabelClass($labelClass)
  {
    self::$labelClass = $labelClass;
  }

  /**
   * Takes in a class that's annotated as a form and builds a form object to manipulate
   *
   * @param object|string $class The object reference or string representation of the
   *                             class name that's annotated as a form
   * @param array         $data  An associative array with keys matching field names
   *                             on the form that is used to pre-populate fields with
   *                             the respective data
   * @param object        $obj   An object with properties that have names the same as
   *                             field names on the form. It is used in the same way
   *                             as `$data`, except it has lower priority when it comes
   *                             time to assign data
   *
   * @return Form A form object with all fields and validators instantiated
   * @throws AnnotationException If the class passed in was not annotated as a form
   */
  public static function create($class, array $data = [], $obj = null)
  {
    $annotated_object = new \ReflectionClass($class);
    // Set up the form annotation overrides
    try {
      $form = self::getFormProperties($class, new Form());
    } catch (AnnotationException $e) {
      throw new AnnotationException($e->getMessage());
    } catch (\Exception $e) {
      throw new AnnotationException(get_class($annotated_object) . " does not have a @form class annotation.");
    }

    // Set up fields on the form and their validators
    foreach ($annotated_object->getProperties() as $property) {
      $validators = [];
      $widget = null;
      $field = null;
      foreach (Annotations::ofProperty($annotated_object->name, $property->name) as $annotation) {
        $annotation_class = get_class($annotation);
        if (key_exists($annotation_class, self::$registeredValidators)) {
          $validators[] = self::processValidator($annotation_class, $annotation);
        } else if (key_exists($annotation_class, self::$registeredWidgets)) {
          $widget = self::processWidget($annotation_class);
        } else if (key_exists($annotation_class, self::$registeredFields)) {
          $field = self::processField($data, $obj, $annotation, $property, $annotation_class);
        }
      }
      if (!is_null($field)) {
        /** @var $field Field */
        $field->finalize($form, $widget, $validators);
        $form->fields[$property->name] = $field;
      }
    }

    return $form;
  }

  /**
   * @param $class
   * @param $form
   *
   * @return Form
   * @throws \Exception
   */
  private static function getFormProperties($class, $form)
  {
    $has_form_annotation = false;
    foreach (Annotations::ofClass($class) as $class_annotation) {
      if ($class_annotation instanceof FormAnnotation) {
        $form->meta = new $class_annotation->meta;
        $form->csrf = $class_annotation->csrf;
        $form->prefix = $class_annotation->prefix;
        $has_form_annotation = true;
      }
    }
    if (!$has_form_annotation) {
      throw new \Exception();
    }

    return $form;
  }

  /**
   * @param $annotation_class
   * @param $annotation
   *
   * @return Validator
   */
  private static function processValidator($annotation_class, $annotation)
  {
    $c = self::$registeredValidators[$annotation_class];

    return new $c($annotation->message);
  }

  /**
   * @param $annotation_class
   *
   * @return Widget
   */
  private static function processWidget($annotation_class)
  {
    $c = self::$registeredWidgets[$annotation_class];

    return new $c();
  }

  /**
   * @param array              $data             Data to pre-populate the field with. This was passed in during form
   *                                             creation. This has
   * @param object             $obj              Data to pre-populate the field with. This was passed in during form
   *                                             creation
   * @param Annotation         $annotation       The current annotation being processed
   * @param ReflectionProperty $property         The current property that is annotated and being processed
   * @param string             $annotation_class String representation of the class used to instantiate this field
   *
   * @return Field The processed field ready to go except for finalization
   */
  private static function processField(array $data, $obj, $annotation, $property, $annotation_class)
  {
    // Default to the php property name for the HTML name if not specified in the annotation
    $annotation->name = $annotation->name ?: $property->name;
    // Instantiate new field using the registered field lookup table
    $c = self::$registeredFields[$annotation_class];
    /** @var $field Field */
    $field = new $c($annotation->label, (array)$annotation);

    // Pre-populate the field with data from the supplied object
    if ($obj && property_exists($obj, $field->name)) {
      $field->data = $obj->{$field->name};
    }
    // Pre-populate the field with data from the supplied array
    if ($data && array_key_exists($field->name, $data)) {
      $field->data = $data[$field->name];
    }

    return $field;
  }
}

