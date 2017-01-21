<?php
/**
 * Created by PhpStorm.
 * User: Nadine
 * Date: 10.04.2016
 * Time: 16:45
 */

namespace app\models;

use yii\console\Exception;

abstract class Enum
{
    private $current_val;
    final public function __construct( $type )
    {
        $class_name = get_class( $this );
        $type = strtoupper( $type );
        if ( constant( "{$class_name}::{$type}" )  === NULL ) {
            throw new Enum_Exception( 'This'.$type.'not found'.$class_name.'.' );
        }
        $this->current_val = constant( "{$class_name}::{$type}" );
    }

    final public function __toString() {
        return $this->current_val;
    }
}

class Enum_Exception extends Exception {}