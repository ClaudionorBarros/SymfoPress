<?php

/*
 * This file is part of the SymfoPress framework.
 * 
 * (c) Carl Alexander <carlalexander@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfoPress\Bridge\WordPress\Memcached;

/**
 * Memcached manager class
 * 
 * @author Carl Alexander <carlalexander@gmail.com>
 */
class Manager
{
    /**
     * Memcache instance
     *
     * @var \Memcache
     */
    protected $memcached;

    /**
     * Prefix for key generation
     * 
     * @var string
     */
    protected $prefix;
    
    /**
     * Constructor
     * 
     * @param string $host Memcached host
     * @param string $port Memcached port
     * @param string $prefix Prefix for key generation
     */
    public function __construct($host, $port, $prefix = '')
    {
        $this->memcached = new \Memcache();
        $this->memcached->addserver($host, $port);
        $this->prefix = $prefix;
    }

    /**
     * Handles setting data in the memcached server
     *
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @return bool
     */
    public function set($key, $value, $expire = 0)
    {
        return $this->memcached->set($this->generateKey($key), $value, false, $expire);
    }

    /**
     * Handles deleting data in the memcached server
     *
     * @param string $key
     * @return bool
     */
    public function delete($key, $value, $expire = 0)
    {
        return $this->memcached->delete($this->generateKey($key));
    }

    /**
     * Handles getting data from the memcached server
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->memcached->get($this->generateKey($key));
    }

    /**
     * Send the flush command to the memcached server
     *
     * @return bool
     */
    public function flush()
    {
        return $this->memcached->flush();
    }

    /**
     * Generates a unique key for the current project so that there's no conflicts.
     *
     * @param string $key
     * @return string
     */
    protected function generateKey($key)
    {
        return md5($this->prefix . $key);
    }
}