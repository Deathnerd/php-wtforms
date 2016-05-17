<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 3/18/2016
 * Time: 5:27 PM
 */
require_once("../vendor/autoload.php");
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use WTForms\Forms;

$reader = new FileCacheReader(
    new AnnotationReader(),
    __DIR__ . "/../runtime",
    $debug = true
);
$registry = new AnnotationRegistry();
$registry->registerFile(__DIR__ . "/IndexTestForm.php");
$registry->registerLoader(function ($class) {

});
Forms::init($reader, $registry);
$form = Forms::create(new \WTForms\Tests\IndexTestForm, $_POST);
if ($_POST) {
  $formisvalid = $form->validate();
} ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <style>
    .green {
      background-color: forestgreen;
    }
  </style>
</head>
<body>
<?
if ($formisvalid) {
  if ($form->name->data) { ?>
    <p>Hi there, <?= $form->name->data ?>!</p>
  <? }
}
if ($form->errors) { ?>
  <div style="border: thin solid red; background-color: salmon">
    <? foreach ($form->errors as $error) { ?>
      <li><?= $error ?></li>
    <? } ?>
  </div>
<? } ?>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
  <?= $form->name->label ?>
  <?= $form->name ?>
  <br>
  <?= $form->wtforms_is_awesome->label("Damn, Felicia!", ['class' => 'green']) ?>
  <?= $form->wtforms_is_awesome ?>
  <br>
  <?= $form->thing_uno->label ?>
  <?= $form->thing_uno ?>
  <br>
  <?= $form->thing_dos->label ?>
  <?= $form->thing_dos ?>
  <input type="submit">
</form>
</body>
</html>