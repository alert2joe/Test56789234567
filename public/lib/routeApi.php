<?php

class routeApi{
        static function getApi($api){
            switch ($api) {
                case 'google':
                    include(APP."lib".DS."routerGoogle.php");
                    return routeGoogle::getInstance();
                break;
                case 'baidu':
                //TODO :: Suport baidu
                break;
                case 'CUSTOM_TSP':
                 //TODO :: do it yourself
                break;
            }

        }
    private static $instances = array();
    protected function __construct() {}
    protected function __clone() {}
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance()
    {
        $cls = get_called_class(); // late-static-bound class name
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }

}