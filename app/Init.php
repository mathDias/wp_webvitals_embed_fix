<?php

namespace App;
final class Init{
    /**
     * Put all the classes inside an array
     * @return array de classes
    */
    public static function  get_services(){
        return array(
            WebvitalsEmbedFix::class
        );
    }

    /**
     * Loop array clases, initialize them, and call the register method.
     */
    public static function register_services(){
        foreach(self::get_services() as $class){
        $service = self::instantiate($class);

        if(method_exists($service,'register')){
            $service->register();
        }
        }
    }

    /**
     * Initialize the class
     * @param class $class class from the service array
     * @return class instance new instance of the class
    */

    private static function instantiate($class){
        return new $class();
    }
}