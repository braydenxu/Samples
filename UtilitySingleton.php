<?php
/**
 * Created by PhpStorm.
 * User: Lei
 * Date: 6/12/15
 * Time: 1:36 PM
 */

/*

    Write a method for a generic utility class with a singleton pattern that will accept an associative
    array, and sort it based on columns and directions specified.

    This would be an example input array (but the method should not be bound to any particular
    array schema):

    $data = array();
    $data[] = array('id' => 1, 'number' => '130', 'street' => 'Battery St', 'unit' => '1', 'rent' => 1200);
    $data[] = array('id' => 1, 'number' => '130', 'street' => 'Battery St', 'unit' => '3', 'rent' => 1800);
    $data[] = array('id' => 1, 'number' => '1049', 'street' => 'Leavenworth St', 'unit' => '11', 'rent' => 800);
    $data[] = array('id' => 1, 'number' => '130', 'street' => 'Battery St', 'unit' => '10', 'rent' => 3400);
    $data[] = array('id' => 1, 'number' => '1059', 'street' => 'Leavenworth St', 'unit' => '10', 'rent' => 1450);
    $data[] = array('id' => 1, 'number' => '130', 'street' => 'Battery St', 'unit' => '5', 'rent' => 1000);

    The method should accept the array, the columns to sort on, the direction of each sort, and an option to sort
    normally or naturally.

 */
class UtilitySingleton
{
    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    public function sortArr()
    {
        $args = func_get_args();
        $data = array_shift($args);

        foreach ($args as $pos => $arg) {
            if (is_string($arg)) {
                $col = array();
                foreach ($data as $key => $row) {
                    $col[$key] = $row[$arg];
                }
                $args[$pos] = $col;
            }
        }


        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

}



$data = array();
$data[] = array('id' => 1, 'number' => '130', 'street' => 'Battery St', 'unit' => '1', 'rent' => 1200);
$data[] = array('id' => 1, 'number' => '130', 'street' => 'Battery St', 'unit' => '3', 'rent' => 1800);
$data[] = array('id' => 1, 'number' => '1049', 'street' => 'Leavenworth St', 'unit' => '11', 'rent' => 800);
$data[] = array('id' => 1, 'number' => '130', 'street' => 'Battery St', 'unit' => '10', 'rent' => 3400);
$data[] = array('id' => 1, 'number' => '1059', 'street' => 'Leavenworth St', 'unit' => '10', 'rent' => 1450);
$data[] = array('id' => 1, 'number' => '130', 'street' => 'Battery St', 'unit' => '5', 'rent' => 1000);


$util = UtilitySingleton::getInstance();

/*
 * array sortArr( array &$array1 [, string column [, mixed $array1_sort_order = SORT_ASC [, mixed $array1_sort_flags = SORT_REGULAR
 *                [, array &$array2 [, string column [, mixed $array2_sort_order = SORT_ASC [, mixed $array2_sort_flags = SORT_REGULAR [, ... ]]]]]]]])
 *
 */
$sortedArr = $util->sortArr($data, 'number', SORT_DESC, SORT_REGULAR, 'unit', SORT_ASC, SORT_NATURAL);

echo "<pre>";
print_r($sortedArr);
echo "</pre>";
