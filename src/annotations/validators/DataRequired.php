<?php
/**
 * Created by PhpStorm.
 * User: Wes Gilleland
 * Date: 1/27/2016
 * Time: 7:52 PM
 */


namespace WTForms\Annotations\Validators;

/**
 * Checks the field's data is 'truthy' otherwise stops the validation chain.
 *
 * This validator checks that the ``data`` attribute on the field is a 'true'
 * value (effectively, it does ``if($field->data)``.) Furthermore, if the data
 * is a string type, a string containing only whitespace characters is
 * considered false.
 *
 * If the data is empty, also removes prior errors (such as processing errors)
 * from the field.
 *
 * **NOTE** Original Python source has a fallback for deprecated ``Required`` class.
 * This port does not have it. You're more than welcome to extend it yourself.
 * @package WTForms\Annotations\Validators
 * @Annotation
 */
class DataRequired extends ValidatorBase
{
}