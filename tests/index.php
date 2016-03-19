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

Annotations::$config['cache'] = new AnnotationCache(__DIR__ . "/runtime");
$annotationManager = Annotations::getManager();
$annotationManager->registry['stringField'] = 'WTForms\Fields\Core\Annotations\StringFieldAnnotation';
$annotationManager->registry['form'] = 'WTForms\FormAnnotation';
$annotationManager->registry['inputRequired'] = 'WTForms\Validators\Annotations\InputRequiredAnnotation';
$annotated_helper = new AnnotatedHelper;

$form = Forms::create($annotated_helper);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>
<form action="">
  <?=$form['first_name']->label->__invoke('Hey, Foo!')?>
  <?=$form['first_name']?>
</form>
</body>
</html>
