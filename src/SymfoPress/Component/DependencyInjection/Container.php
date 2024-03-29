<?php

/*
 * This file is part of the SymfoPress framework.
 * 
 * (c) Carl Alexander <carlalexander@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfoPress\Component\DependencyInjection;

/**
 * PHP 5.3 DI Container based on Pimple by Fabien Potencier.
 * 
 * @author Carl Alexander <carlalexander@gmail.com>
 */
class Container implements \ArrayAccess
{
    /**
     * Object/Variables stored in the container.
     *
     * @var mixed
     */
    private $values = array();

    /**
     * Sets a parameter or an object.
     *
     * Objects must be defined as Closures.
     *
     * Allowing any PHP callable leads to difficult to debug problems
     * as function names (strings) are callable (creating a function with
     * the same a name as an existing parameter would break your container).
     *
     * @param string $id    The unique identifier for the parameter or object
     * @param mixed  $value The value of the parameter or a closure to defined an object
     */
    function offsetSet($id, $value)
    {
        $this->values[$id] = $value;
    }

    /**
     * Gets a parameter or an object.
     *
     * @param  string $id The unique identifier for the parameter or object
     *
     * @return mixed  The value of the parameter or an object
     *
     * @throws InvalidArgumentException if the identifier is not defined
     */
    function offsetGet($id)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        return $this->values[$id] instanceof \Closure ? $this->values[$id]($this) : $this->values[$id];
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param  string $id The unique identifier for the parameter or object
     *
     * @return Boolean
     */
    function offsetExists($id)
    {
        return isset($this->values[$id]);
    }

    /**
     * Unsets a parameter or an object.
     *
     * @param  string $id The unique identifier for the parameter or object
     */
    function offsetUnset($id)
    {
        unset($this->values[$id]);
    }

    /**
     * Returns a closure that stores the result of the given closure for
     * uniqueness in the scope of this instance of Pimple.
     *
     * @param \Closure $callable A closure to wrap for uniqueness
     *
     * @return \Closure The wrapped closure
     */
    function share(\Closure $callable)
    {
        return function ($c) use ($callable) {
            static $object;

            if (is_null($object)) {
                $object = $callable($c);
            }

            return $object;
        };
    }

    /**
     * Protects a callable from being interpreted as a service.
     *
     * This is useful when you want to store a callable as a parameter.
     *
     * @param \Closure $callable A closure to protect from being evaluated
     *
     * @return \Closure The protected closure
     */
    function protect(\Closure $callable)
    {
        return function ($c) use ($callable) {
            return $callable;
        };
    }

    /**
     * Gets a parameter or the closure defining an object.
     *
     * @param  string $id The unique identifier for the parameter or object
     *
     * @return mixed  The value of the parameter or the closure defining an object
     *
     * @throws InvalidArgumentException if the identifier is not defined
     */
    function raw($id)
    {
        if (!isset($this->values[$id])) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        return $this->values[$id];
    }
}