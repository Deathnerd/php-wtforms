<?php
  /**
   * Created by PhpStorm.
   * User: Wes Gilleland
   * Date: 12/30/2015
   * Time: 3:40 PM
   */

if(!function_exists('e')){
  /**
   * Blatant ripoff of Laravel and Twig's HTML escape function
   *
   * @param $string string The string to escape
   *
   * @return string The escaped string
   */
  function e($string){
    return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
  }
}