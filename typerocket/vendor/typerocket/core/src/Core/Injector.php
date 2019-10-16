<?php


namespace TypeRocket\Core;


class Injector
{

    protected static $list = [];

    /**
     * Resolve Class
     *
     * @param string $class_name
     * @return mixed|null
     */
    public static function resolve($class_name) {
        if(array_key_exists($class_name, self::$list)) {
            $single = self::$list[$class_name]['singleton_instance'];

            if($single) {
                return $single;
            }

            $instance = call_user_func(self::$list[$class_name]['callback']);

            if(!empty(self::$list[$class_name]['make_singleton'])) {
                self::$list[$class_name]['singleton_instance'] = $instance;
            }

            return $instance;
        }

        return null;
    }

    /**
     * Register Class
     *
     * @param string $class_name
     * @param callable $callback
     * @param bool $singleton
     *
     * @return bool
     */
    public static function register($class_name, $callback, $singleton = false)
    {
        if(!empty(self::$list[$class_name])) {
            return false;
        }

        self::$list[$class_name] = [
            'callback' => $callback,
            'make_singleton' => $singleton,
            'singleton_instance' => null
        ];

        return true;
    }

    /**
     * Resolve Singleton
     *
     * @param string $class_name
     *
     * @return mixed|null
     */
    public static function findOrNewSingleton($class_name)
    {
        self::register($class_name, function() use ($class_name) {
            return new $class_name;
        }, true);

        return self::resolve($class_name);
    }

    /**
     * Destroy By Key
     *
     * @param string $class_name
     *
     * @return bool
     */
    public static function destroy($class_name)
    {
        if(array_key_exists($class_name, self::$list)) {
            unset(self::$list[$class_name]['singleton_instance']);
            unset(self::$list[$class_name]);

            return true;
        }

        return false;
    }

}