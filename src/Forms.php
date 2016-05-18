<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/14/2016
 * Time: 7:48 PM
 */

namespace WTForms;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use ReflectionProperty;
use WTForms\Fields\Core\Field;
use WTForms\Validators\Validator;
use WTForms\Widgets\Core\Widget;


class Forms
{
  /**
   * @var AnnotationReader
   */
  private static $annotation_reader;
  /**
   * @var AnnotationRegistry
   */
  private static $annotation_registry;

  private static $registeredValidators = [
      'WTForms\Annotations\Validators\AnyOf'         => 'WTForms\Validators\AnyOf',
      'WTForms\Annotations\Validators\DataRequired'  => 'WTForms\Validators\DataRequired',
      'WTForms\Annotations\Validators\EqualTo'       => 'WTForms\Validators\EqualTo',
      'WTForms\Annotations\Validators\InputRequired' => 'WTForms\Validators\InputRequired',
      'WTForms\Annotations\Validators\IPAddress'     => 'WTForms\Validators\IPAddress',
      'WTForms\Annotations\Validators\Length'        => 'WTForms\Validators\Length',
      'WTForms\Annotations\Validators\MacAddress'    => 'WTForms\Validators\MacAddress',
      'WTForms\Annotations\Validators\NoneOf'        => 'WTForms\Validators\NoneOf',
      'WTForms\Annotations\Validators\NumberRange'   => 'WTForms\Validators\NumberRange',
      'WTForms\Annotations\Validators\Optional'      => 'WTForms\Validators\Optional',
      'WTForms\Annotations\Validators\Regexp'        => 'WTForms\Validators\Regexp',
      'WTForms\Annotations\Validators\URL'           => 'WTForms\Validators\URL',
      'WTForms\Annotations\Validators\UUID'          => 'WTForms\Validators\UUID',
  ];
  private static $registeredFields = [
      'WTForms\Annotations\Fields\Core\BooleanField'        => 'WTForms\Fields\Core\BooleanField',
      'WTForms\Annotations\Fields\Core\DateField'           => 'WTForms\Fields\Core\DateField',
      'WTForms\Annotations\Fields\Core\DateTimeField'       => 'WTForms\Fields\Core\DateTimeField',
      'WTForms\Annotations\Fields\Core\DecimalField'        => 'WTForms\Fields\Core\DecimalField',
      'WTForms\Annotations\Fields\Core\FloatField'          => 'WTForms\Fields\Core\FloatField',
      'WTForms\Annotations\Fields\Core\FieldList'           => 'WTForms\Fields\Core\FieldList',
      'WTForms\Annotations\Fields\Core\IntegerField'        => 'WTForms\Fields\Core\IntegerField',
      'WTForms\Annotations\Fields\Core\RadioField'          => 'WTForms\Fields\Core\RadioField',
      'WTForms\Annotations\Fields\Core\SelectField'         => 'WTForms\Fields\Core\SelectField',
      'WTForms\Annotations\Fields\Core\SelectMultipleField' => 'WTForms\Fields\Core\SelectMultipleField',
      'WTForms\Annotations\Fields\Core\StringField'         => 'WTForms\Fields\Core\StringField',
      'WTForms\Annotations\Fields\HTML5\DateField'          => 'WTForms\Fields\HTML5\DateField',
      'WTForms\Annotations\Fields\HTML5\DateTimeField'      => 'WTForms\Fields\HTML5\DateTimeField',
      'WTForms\Annotations\Fields\HTML5\DateTimeLocalField' => 'WTForms\Fields\HTML5\DateTimeLocalField',
      'WTForms\Annotations\Fields\HTML5\DecimalField'       => 'WTForms\Fields\HTML5\DecimalField',
      'WTForms\Annotations\Fields\HTML5\DecimalRangeField'  => 'WTForms\Fields\HTML5\DecimalRangeField',
      'WTForms\Annotations\Fields\HTML5\EmailField'         => 'WTForms\Fields\HTML5\EmailField',
      'WTForms\Annotations\Fields\HTML5\IntegerField'       => 'WTForms\Fields\HTML5\IntegerField',
      'WTForms\Annotations\Fields\HTML5\IntegerRangeField'  => 'WTForms\Fields\HTML5\IntegerRangeField',
      'WTForms\Annotations\Fields\HTML5\SearchField'        => 'WTForms\Fields\HTML5\SearchField',
      'WTForms\Annotations\Fields\HTML5\TelField'           => 'WTForms\Fields\HTML5\TelField',
      'WTForms\Annotations\Fields\HTML5\URLField'           => 'WTForms\Fields\HTML5\URLField',
      'WTForms\Annotations\Fields\Simple\FileField'         => 'WTForms\Fields\Simple\FileField',
      'WTForms\Annotations\Fields\Simple\HiddenField'       => 'WTForms\Fields\Simple\HiddenField',
      'WTForms\Annotations\Fields\Simple\PasswordField'     => 'WTForms\Fields\Simple\PasswordField',
      'WTForms\Annotations\Fields\Simple\SubmitField'       => 'WTForms\Fields\Simple\SubmitField',
      'WTForms\Annotations\Fields\Simple\TextAreaField'     => 'WTForms\Fields\Simple\TextAreaField',
  ];

  private static $namespaces = [
      'WTForms\Annotations\Validators',
      'WTForms\Annotations\Fields\Core',
      'WTForms\Annotations\Fields\HTML5',
      'WTForms\Annotations\Fields\Simple',
      'WTForms\Annotations\Widgets\Core',
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
   * @return AnnotationReader
   */
  public static function getAnnotationReader()
  {
    return self::$annotation_reader;
  }

  /**
   * @param AnnotationReader $annotation_reader
   */
  public static function setAnnotationReader(AnnotationReader $annotation_reader)
  {
    self::$annotation_reader = $annotation_reader;
  }

  /**
   * @return AnnotationRegistry
   */
  public static function getAnnotationRegistry()
  {
    return self::$annotation_registry;
  }

  /**
   * @param AnnotationRegistry $annotation_registry
   */
  public static function setAnnotationRegistry(AnnotationRegistry $annotation_registry)
  {
    self::$annotation_registry = $annotation_registry;
  }

  /**
   * @return array
   */
  public static function getNamespaces()
  {
    return self::$namespaces;
  }

  /**
   * @param array $namespaces
   */
  public static function setNamespaces($namespaces)
  {
    self::$namespaces = $namespaces;
  }

  /**
   * @param array $namespaces
   */
  public static function addNamespaces(array $namespaces)
  {
    self::setNamespaces(array_merge(self::$namespaces, $namespaces));
  }

  /**
   * @param $class
   */
  public static function addClassToNamespaces($class)
  {
    if (!$class instanceof \ReflectionClass) {
      $class = new \ReflectionClass($class);
    }
    self::addNamespace($class->getNamespaceName());
  }

  /**
   * @param string $namespace
   */
  public static function addNamespace($namespace)
  {
    if ($namespace != "" && !in_array($namespace, self::$namespaces)) {
      self::$namespaces[] = $namespace;
    }
  }

  public static function init(Reader $annotationReader, AnnotationRegistry $annotationRegistry)
  {
    self::$annotation_reader = $annotationReader;
    self::$annotation_registry = $annotationRegistry;
    foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . "/annotations")) as $filename) {
      if ($filename->isDir()) {
        continue;
      }
      $file = $filename->getPathname();
      self::$annotation_registry->registerFile($file);
    }
  }

  /**
   * Takes in a class that's annotated as a form and builds a form object to manipulate
   *
   * @param object|string $class    The object reference or string representation of the
   *                                class name that's annotated as a form
   * @param array         $formdata Data that was passed in from the user on form submission,
   *                                e.g. `$_POST`
   * @param array         $data     An associative array with keys matching field names
   *                                on the form that is used to pre-populate fields with
   *                                the respective data
   * @param object        $obj      An object with properties that have names the same as
   *                                field names on the form. It is used in the same way
   *                                as `$data`, except it has lower priority when it comes
   *                                time to assign data
   *
   * @return Form A form object with all fields and validators instantiated
   * @throws AnnotationException If the class passed in was not annotated as a form
   */
  public static function create($class, array $formdata = [], array $data = [], $obj = null)
  {
    if (!self::$annotation_registry || !self::$annotation_reader ||
        !(self::$annotation_registry instanceof AnnotationRegistry) ||
        !(self::$annotation_reader instanceof Reader)
    ) {
      throw new \RuntimeException("Forms class has not been initialized!");
    }
    $annotated_object = new \ReflectionClass($class);
    // Set up the form annotation overrides
    try {
      $form = self::getFormProperties($annotated_object);
    } catch (AnnotationException $e) {
      throw new AnnotationException($e->getMessage());
    } catch (\Exception $e) {
      throw new AnnotationException(get_class($annotated_object) . " does not have a @Form class annotation.");
    }

    // Find all field annotations and convert them to their
    // usable object forms and attach to the form
    foreach ($annotated_object->getProperties() as $property) {
      foreach (self::$annotation_reader->getPropertyAnnotations($property) as $annotation) {
        $annotation_class = get_class($annotation);

        if (array_key_exists($annotation_class, self::$registeredFields)) {
          $field = self::resolveField($annotation, $property);
          $field_name = $field->name ?: $field->id;

          if ($obj && property_exists($obj, $field_name)) {
            $field->process($formdata, $obj->{$field_name});
          } elseif (array_key_exists($field_name, $data)) {
            $field->process($formdata, $data[$field_name]);
          } else {
            $field->process($formdata);
          }

          $form[$field_name] = $field;
        }
      }
    }

    return $form;
  }

  public static function createWithOptions($class, array $options = [], $class, array $formdata = [], array $data = [], $obj = null)
  {
    if (!self::$annotation_registry || !self::$annotation_reader ||
        !(self::$annotation_registry instanceof AnnotationRegistry) ||
        !(self::$annotation_reader instanceof Reader)
    ) {
      throw new \RuntimeException("Forms class has not been initialized!");
    }
    $annotated_object = new \ReflectionClass($class);
    // Set up the form annotation overrides
    try {
      $form = self::getFormProperties($annotated_object, $options);
    } catch (AnnotationException $e) {
      throw new AnnotationException($e->getMessage());
    } catch (\Exception $e) {
      throw new AnnotationException(get_class($annotated_object) . " does not have a @Form class annotation.");
    }

    // Find all field annotations and convert them to their
    // usable object forms and attach to the form
    foreach ($annotated_object->getProperties() as $property) {
      foreach (self::$annotation_reader->getPropertyAnnotations($property) as $annotation) {
        $annotation_class = get_class($annotation);

        if (array_key_exists($annotation_class, self::$registeredFields)) {
          $field = self::resolveField($annotation, $property);
          $field_name = $field->name ?: $field->id;

          if ($obj && property_exists($obj, $field_name)) {
            $field->process($formdata, $obj->{$field_name});
          } elseif (array_key_exists($field_name, $data)) {
            $field->process($formdata, $data[$field_name]);
          } else {
            $field->process($formdata);
          }

          $form[$field_name] = $field;
        }
      }
    }

    return $form;
  }

  /**
   * @param \ReflectionClass $class
   *
   * @return \WTForms\Form
   * @throws \Exception
   */
  private static function getFormProperties(\ReflectionClass $class, array $options = [])
  {
    foreach (self::$annotation_reader->getClassAnnotations($class) as $class_annotation) {
      if ($class_annotation instanceof \WTForms\Annotations\Form) {
        if (array_key_exists("meta", $options)) {
          $class_annotation->meta = $options['meta'];
        }
        if (array_key_exists("prefix", $options)) {
          $class_annotation->prefix = $options['prefix'];
        }
        $form = new \WTForms\Form([], $class_annotation->prefix, new $class_annotation->meta);
        $form->csrf = $class_annotation->csrf;

        return $form;
      }
    }
    throw new \Exception();
  }

  /**
   * Resolve a field annotation to the concrete Field object it represents
   *
   * @param Annotations\Field  $annotation
   * @param ReflectionProperty $property
   *
   * @return mixed
   */
  private static function resolveField(\WTForms\Annotations\Field $annotation, \ReflectionProperty $property)
  {
    /**
     * @var $concrete_class Field
     */
    $concrete_class = self::$registeredFields[get_class($annotation)];
    if (!$concrete_class) {
      throw new \RuntimeException("Annotation class " . get_class($annotation) . " is not mapped to any concrete class!");
    }
    $validators = [];
    foreach ($annotation->validators as $validator_annotation) {
      $validators[] = self::resolveFieldValidator($validator_annotation);
    }
    $annotation->widget = self::resolveFieldWidget($annotation->widget);
    $options = (array)$annotation;
    $options['validators'] = $validators;
    $options['name'] = $options['name'] ?: $property->getName();

    return new $concrete_class($annotation->label, $options);
  }

  public static function resolveFieldForFieldList(\WTForms\Annotations\Field $annotation, array $properties = [])
  {
    $concrete_class = self::$registeredFields[get_class($annotation)];
    if (!$concrete_class) {
      throw new \RuntimeException("Annotation class " . get_class($annotation) . " is not mapped to any concrete class!");
    }
    $validators = [];
    foreach ($annotation->validators as $validator_annotation) {
      $validators[] = self::resolveFieldValidator($validator_annotation);
    }
    $annotation->widget = self::resolveFieldWidget($annotation->widget);
    $options = (array)$annotation;
    $options['validators'] = $validators;
    $options = array_merge($options, $properties);

    return new $concrete_class($annotation->label, $options);
  }

  /**
   * Resolve a Validator annotation attached to a field into the proper Validator object
   *
   * @param Annotations\Validators\ValidatorBase $validator_annotation
   *
   * @return Validator The Validator the Annotation resolved to
   */
  private static function resolveFieldValidator(\WTForms\Annotations\Validators\ValidatorBase $validator_annotation)
  {
    $concrete_class = self::$registeredValidators[get_class($validator_annotation)];
    if (!$concrete_class) {
      throw new \RuntimeException("Annotation class " . get_class($validator_annotation) . " is not mapped to any concrete class!");
    }
    $options = (array)$validator_annotation;
    $message = $options['message'];
    unset($options['message']);

    return new $concrete_class($message, $options);
  }

  /**
   * Resolves a field's widget to a concrete widget object
   *
   * @param \ReflectionClass|string $widget
   *
   * @return Widget The new widget
   */
  private static function resolveFieldWidget($widget)
  {
    return new $widget();
  }
}

