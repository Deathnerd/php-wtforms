<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 3/14/2016
 * Time: 8:11 PM
 */
require_once('../vendor/autoload.php');

/*$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();*/

use Composer\Autoload\ClassLoader;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use WTForms\Forms;
use WTForms\Tests\SupportingClasses\Person;

$auto_loader = new ClassLoader();
$auto_loader->addPsr4("mindplay\\demo\\", __DIR__ . "../vendor/mindplay/annotations/demo/annotations");
$auto_loader->addPsr4("wtforms\\tests\\supporting_classes\\", __DIR__ . "/supporting_classes");
$auto_loader->addPsr4("Deathnerd\\WTForms\\", __DIR__ . "../src");
$auto_loader->register();


Annotations::$config['cache'] = new AnnotationCache(__DIR__ . "/runtime");
$annotationManager = Annotations::getManager();
$annotationManager->registry['length'] = 'mindplay\demo\annotations\LengthAnnotation';
$annotationManager->registry['required'] = 'mindplay\demo\annotations\RequiredAnnotation';
$annotationManager->registry['text'] = 'mindplay\demo\annotations\TextAnnotation';
$annotationManager->registry['stringfield'] = 'WTForms\Fields\Core\Annotations\StringFieldAnnotation';
$annotationManager->registry['form'] = 'WTForms\FormAnnotation';

$person = new Person;

Forms::create($person);