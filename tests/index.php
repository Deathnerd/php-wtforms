<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 3/18/2016
 * Time: 5:27 PM
 */
require_once("../vendor/autoload.php");
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use WTForms\Tests\SupportingClasses\AnnotatedHelper;
use WTForms\Tests\SupportingClasses\Helper;
use WTForms\Forms;
use DebugBar\StandardDebugBar;

Annotations::$config['cache'] = new AnnotationCache(__DIR__ . "/runtime");
$annotationManager = Annotations::getManager();
$annotationManager->registry['stringField'] = 'WTForms\Fields\Core\Annotations\StringFieldAnnotation';
$annotationManager->registry['form'] = 'WTForms\FormAnnotation';
$annotationManager->registry['inputRequired'] = 'WTForms\Validators\Annotations\InputRequiredAnnotation';
$annotated_helper = new AnnotatedHelper;

$debugbar = new StandardDebugBar();
$debugbarRenderer = $debugbar->getJavascriptRenderer();
$debugbar['time']->startMeasure('form_create', "Creating a form");
$form = Forms::create($annotated_helper);
$debugbar['time']->stopMeasure('form_create');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <?=$debugbarRenderer->renderHead()?>
</head>
<body>
<form action="">
  <?=$form['first_name']->label('Hey, Foo!')?>
  <?=$form['first_name']?>
  <?=$form->first_name->label?>
</form>
<?=$debugbarRenderer->render()?>
</body>
</html>
