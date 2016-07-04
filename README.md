[![Build Status](https://travis-ci.org/Deathnerd/php-wtforms.svg?branch=master)](https://travis-ci.org/Deathnerd/php-wtforms)
# php-wtforms
A PHP rewrite of the fantastic Python library WTForms. 

I do not take credit for the idea behind WTForms or anything related to its original implementation. I just bastardized it and ported it to PHP. 

# Install
Add the following line to the `require` portion of your `composer.json`
```json
"deathnerd/php-wtforms":"0.5.0"
```
or if you're feeling froggy, pull in the cutting edge master release
```json
"deathnerd/php-wtforms":"dev-master"
```
or run the following command from your favorite terminal
`composer require deathnerd/php-wtforms:0.5.0`
for the stable version and 
`compser require deathnerd/php-wtforms:dev-master`
for the bleeding edge dev release.

Note: The dev-master version is not guaranteed to be stable.

# Quick Start
To create a simple login-form it's as simple as this:
###LogInForm.php
```php
<?php
namespace MyNamespace\Forms;

use WTForms\Form;
use WTForms\Fields\Simple\PasswordField;
use WTForms\Fields\Simple\SubmitField;
use WTForms\Fields\Core\StringField;
use WTForms\Validators\InputRequired;
use WTForms\Validators\Length;
use WTForms\Validators\Regexp;

class LogInForm extends Form {
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->username = new StringField(["validators"=>[
            new InputRequired("You must provide a username"),
            new Length("Usernames must be between %(min) and %(max) characters long", ["min"=>3, "max"=>10]),
            new Regexp("Usernames may not contain the following characters: ;-/@", ["regex"=>'/^((?!;\\-\\/@).)*$/'])
        ]]);
        $this->password = new PasswordField(["validators"=>[
            new InputRequired("Can't log in without a password!"),
            new Length("Passwords must be at least %(min) characters in length", ["min"=>5])
        ]]);
        $this->submit = new SubmitField(["label"=>"Submit"]);
    }
}
```
###login.php
```php
<?php
require_once 'vendor/autoload.php';

use MyNamespace\Forms\LogInForm;

$form = LogInForm::create(["formdata" => $_POST]);

if ($_POST) {
    if ($form->validate()) {
        // do stuff to log in and authenticate the user
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LogIn Form</title>
</head>
<body>
<?php
if ($form->errors) {
    ?>
    <ul class="errors">
        <?php
        foreach ($form->errors as $field_name => $errors) {
            foreach ($errors as $field_error) { ?>
                <li><?= $field_name ?> - <?= $field_error ?></li>
                <?
            }
        }
        ?>
    </ul>
    <?php
}
?>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
    <?= $form->username->label ?>
    <?= $form->username ?>
    <?= $form->password->label ?>
    <?= $form->password ?>
    <?= $form->submit ?>
</form>
</body>
</html>
```

And that's that. More in-depth examples and actual documentation are coming in the future. For now, look in the `tests` directory for ideas on how to do things
