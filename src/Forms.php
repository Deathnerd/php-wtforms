<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/14/2016
 * Time: 7:48 PM
 */

namespace Deathnerd\WTForms;

use Deathnerd\WTForms\Fields\Core\Field;
use mindplay\annotations\AnnotationException;
use mindplay\annotations\Annotations;


class Forms
{
  private static $instance = null;

  private static $registeredValidators = [
      'Deathnerd\WTForms\Validators\Annotations\AnyOfAnnotation'         => 'Deathnerd\WTForms\Validators\AnyOf',
      'Deathnerd\WTForms\Validators\Annotations\DataRequiredAnnotation'  => 'Deathnerd\WTForms\Validators\DataRequired',
      'Deathnerd\WTForms\Validators\Annotations\EqualToAnnotation'       => 'Deathnerd\WTForms\Validators\EqualTo',
      'Deathnerd\WTForms\Validators\Annotations\InputRequiredAnnotation' => 'Deathnerd\WTForms\Validators\InputRequired',
      'Deathnerd\WTForms\Validators\Annotations\IPAddressAnnotation'     => 'Deathnerd\WTForms\Validators\IPAddress',
      'Deathnerd\WTForms\Validators\Annotations\LengthAnnotation'        => 'Deathnerd\WTForms\Validators\Length',
      'Deathnerd\WTForms\Validators\Annotations\MacAddressAnnotation'    => 'Deathnerd\WTForms\Validators\MacAddress',
      'Deathnerd\WTForms\Validators\Annotations\NoneOfAnnotation'        => 'Deathnerd\WTForms\Validators\NoneOf',
      'Deathnerd\WTForms\Validators\Annotations\NumberRangeAnnotation'   => 'Deathnerd\WTForms\Validators\NumberRange',
      'Deathnerd\WTForms\Validators\Annotations\OptionalAnnotation'      => 'Deathnerd\WTForms\Validators\Optional',
      'Deathnerd\WTForms\Validators\Annotations\RegexpAnnotation'        => 'Deathnerd\WTForms\Validators\Regexp',
      'Deathnerd\WTForms\Validators\Annotations\URLAnnotation'           => 'Deathnerd\WTForms\Validators\URL',
      'Deathnerd\WTForms\Validators\Annotations\UUIDAnnotation'          => 'Deathnerd\WTForms\Validators\UUID',
  ];
  private static $registeredFields = [
      'Deathnerd\WTForms\Fields\Core\BooleanField',
      'Deathnerd\WTForms\Fields\Core\DateField',
      'Deathnerd\WTForms\Fields\Core\DateTimeField',
      'Deathnerd\WTForms\Fields\Core\DecimalField',
      'Deathnerd\WTForms\Fields\Core\FloatField',
      'Deathnerd\WTForms\Fields\Core\IntegerField',
      'Deathnerd\WTForms\Fields\Core\RadioField',
      'Deathnerd\WTForms\Fields\Core\SelectField',
      'Deathnerd\WTForms\Fields\Core\SelectMultipleField',
      'Deathnerd\WTForms\Fields\Core\Annotations\StringFieldAnnotation' => 'Deathnerd\WTForms\Fields\Core\StringField',
      'Deathnerd\WTForms\Fields\HTML5\DateField',
      'Deathnerd\WTForms\Fields\HTML5\DateTimeField',
      'Deathnerd\WTForms\Fields\HTML5\DateTimeLocalField',
      'Deathnerd\WTForms\Fields\HTML5\DecimalField',
      'Deathnerd\WTForms\Fields\HTML5\DecimalRangeField',
      'Deathnerd\WTForms\Fields\HTML5\EmailField',
      'Deathnerd\WTForms\Fields\HTML5\IntegerField',
      'Deathnerd\WTForms\Fields\HTML5\IntegerRangeField',
      'Deathnerd\WTForms\Fields\HTML5\SearchField',
      'Deathnerd\WTForms\Fields\HTML5\TelField',
      'Deathnerd\WTForms\Fields\HTML5\URLField',
      'Deathnerd\WTForms\Fields\Simple\FileField',
      'Deathnerd\WTForms\Fields\Simple\HiddenField',
      'Deathnerd\WTForms\Fields\Simple\PasswordField',
      'Deathnerd\WTForms\Fields\Simple\SubmitField',
      'Deathnerd\WTForms\Fields\Simple\TextAreaField',
  ];

  private static $registeredWidgets = [
      'Deathnerd\WTForms\Widgets\Core\CheckboxInput',
      'Deathnerd\WTForms\Widgets\Core\FileInput',
      'Deathnerd\WTForms\Widgets\Core\HiddenInput',
      'Deathnerd\WTForms\Widgets\Core\List',
      'Deathnerd\WTForms\Widgets\Core\Option',
      'Deathnerd\WTForms\Widgets\Core\PasswordInput',
      'Deathnerd\WTForms\Widgets\Core\RadioInput',
      'Deathnerd\WTForms\Widgets\Core\Select',
      'Deathnerd\WTForms\Widgets\Core\SubmitInput',
      'Deathnerd\WTForms\Widgets\Core\Table',
      'Deathnerd\WTForms\Widgets\Core\TextArea',
      'Deathnerd\WTForms\Widgets\Core\TextInput',
  ];

  private static $labelClass = 'Deathnerd\WTForms\Fields\Label';

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
   * @param        $class
   * @param array  $data
   * @param object $obj
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
          $c = self::$registeredValidators[$annotation_class];
          $validators[] = new $c($annotation->message);
        } else if (key_exists($annotation_class, self::$registeredWidgets)) {
          $widget = new $annotation_class();
        } else if (key_exists($annotation_class, self::$registeredFields)) {
          $annotation->name = $annotation->name ?: $property->name;
          $c = self::$registeredFields[$annotation_class];
          $field = new $c($annotation->label, (array)$annotation);
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
}

