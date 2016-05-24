<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 3/18/2016
 * Time: 5:27 PM
 */
require_once("../vendor/autoload.php");
use WTForms\Form;
use WTForms\Validators\DataRequired;
use WTForms\Validators\Optional;
use WTForms\Validators\UUID;
use WTForms\Fields\Core\StringField;
use WTForms\Fields\Core\FloatField;
use WTForms\Fields\Core\IntegerField;
use WTForms\Validators\NumberRange;

class IndexForm extends Form
{
  public function __construct(array $options)
  {
    parent::__construct($options);
    $this->name = new StringField(["name"        => "name",
                                   "label"       => "What's your name?",
                                   "validators"  => [new DataRequired],
                                   "class"       => "form-control",
                                   "placeholder" => "What's your name little girl?"
    ]);
    $this->lastName = new StringField(["name"       => "lastName",
                                       "label"      => "Your last name?",
                                       "validators" => [new Optional]]);
    $this->UUID = new StringField(["name"       => "UUID",
                                   "label"      => "Your UUID",
                                   "validators" => [new UUID]]);
    $this->age = new FloatField(["name"       => "age",
                                 "label"      => "Your age",
                                 "validators" => [new NumberRange("", ["min" => 18])]]);
  }
}

$startTime = microtime();
$form = new IndexForm(["formdata" => $_POST]);
$form->foo = new IntegerField(['label'   => "Foobar!",
                               "default" => function () {
                                 return rand(0, 10);
                               }]);
$endTime = microtime();

print "The time was: " . (($endTime - $startTime) * 1000) . " milliseconds\n"

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>
<form action="<?= $_SERVER['PHP_SELF'] ?>">
  <?
  $starTime = microtime();
  ?>
  <?= $form->name->label ?>

  <?= $form->name ?>

  <?= $form->foo->label ?>

  <?= $form->foo ?>
  <?
  $endTime = microtime();
  ?>

  <?= ($endTime - $startTime) * 1000 . " milliseconds to render" ?>

</form>
</body>
</html>
