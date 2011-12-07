<?php

/*
 * This file is part of the SymfoPress framework.
 * 
 * (c) Carl Alexander <carlalexander@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfoPress\Component\Application;

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\Routing\RouteCollection;
use SymfoPress\Component\DependencyInjection\Container;
use SymfoPress\Component\Extension\ExtensionInterface;

/**
 * Application class that powers the framework
 *
 * @author Carl Alexander <carlalexander@gmail.com>
 */
class Application extends Container implements ApplicationInterface
{
    /**
     * @var Boolean
     */
    protected $booted;
    
    /**
     * An array of extensions
     * 
     * @var array
     */
    protected $extensions;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->booted = false;

        $this['autoloader'] = $this->share(function () {
           $loader =  new UniversalClassLoader();
           $loader->register();

           return $loader;
        });

        $this['routes'] = $this->share(function () {
            return new RouteCollection();
        });
        
        $this['debug'] = false;
        $this['charset'] = 'UTF-8';
        $this['request.http_port'] = 80;
        $this['request.https_port'] = 443;
        
        $this['dbhost'] = defined('DB_HOST') ? DB_HOST : '';
        $this['dbname'] = defined('DB_NAME') ? DB_NAME : '';
        $this['dbuser'] = defined('DB_USER') ? DB_USER : '';
        $this['dbpassword'] = defined('DB_PASSWORD') ? DB_PASSWORD : '';
        
        $this->init();
    }
    
    /**
     * Intialization tasks
     */
    protected function init()
    {
        // Mount extensions
        $this->mountExtensions();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->booted === true) {
            return;
        }
        
        foreach ($this->getExtensions() as $extension) {
            $extension->boot();
        }
        
        $this->booted = true;
    }

    /**
     * {@inheritdoc}
     */
    public function registerExtensions()
    {
        return array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function mountExtensions()
    {
        $this->extensions = array();
        
        foreach ($this->registerExtensions() as $extension) {
            $this->mount($extension);
        }
    }
  
    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
    
    /**
     * {@inheritdoc}
     */
    public function mount(ExtensionInterface $extension, array $values = array())
    {
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
        
        $extension->connect($this);
        $this->extensions[] = $extension;
    }
}