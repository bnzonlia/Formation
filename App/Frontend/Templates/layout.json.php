<?php
/**
 * Created by PhpStorm.
 * User: bnzonlia
 * Date: 19/04/2017
 * Time: 17:39
 */
 ?>

<?php
/**
 * @var array $content Data set in json content page
 */
?>

<?php
$layout[ 'code' ]  = isset( $code ) ? $code : 0;
$layout[ 'error' ] = isset( $error ) ? $error : '';
$layout[ 'content' ]      = $content;
return $layout;
?>