<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/14/2016
 * Time: 8:11 PM
 */
require_once('../vendor/autoload.php');

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses;

$reader = new FileCacheReader(
    new AnnotationReader(),
    __DIR__ . "/runtime",
    $debug = true
);
$foo = new SupportingClasses\Foo;
$bar = new WTForms\Tests\SupportingClasses\Bar;
$foo->baz = [1,2,3,4,5];
$foo->shazbot = function($var){$var += 2; echo $var; return $var;};
$foo_serialized = serialize($foo);
$bar_serialized = serialize($bar);
echo $foo_serialized."\n";
echo $bar_serialized;
$foo_unserialized = unserialize($foo_serialized);
$bar_unserialized = unserialize($bar_serialized);
echo "Done!";
$registry = new AnnotationRegistry();
$registry->registerFile(__DIR__ . "/supporting_classes/Foo.php");
$registry->registerFile(__DIR__ . "/supporting_classes/Bar.php");
$registry->registerFile(__DIR__ . "/../src/annotations/Extend.php");
$registry->registerFile(__DIR__ . "/../src/annotations/Field.php");
$registry->registerFile(__DIR__ . "/../src/annotations/Form.php");
$foo_reflection = new ReflectionClass('WTForms\Tests\SupportingClasses\Foo');
$bar_reflection = new ReflectionClass('WTForms\Tests\SupportingClasses\Bar');

$resolved = resolveAnnotatedClass($bar_reflection, $reader);

function resolveAnnotatedClass(ReflectionClass $reflection, \Doctrine\Common\Annotations\Reader $reader)
{
  $extend = $reader->getClassAnnotation($reflection, new WTForms\Annotations\Extend);

  if($extend){
    $c =  resolveAnnotatedClass(new ReflectionClass($extend->parent_annotation), $reader);
  } else {
    $c = new stdClass($reflection->getName());
  }
  foreach($reflection->getDefaultProperties() as $property_name=>$property_value){
    $c->$property_name = $property_value;
  }
  return $c;
}