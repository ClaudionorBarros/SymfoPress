<?php

/*
 * This file is part of the SymfoPress framework.
 * 
 * (c) Carl Alexander <carlalexander@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfoPress\Component\Extension;

use SymfoPress\Component\Application\ApplicationInterface;

/**
 * Base class for application extension
 * 
 * @author Carl Alexander <carlalexander@gmail.com>
 */
abstract class Extension implements ExtensionInterface
{
    /**
     * The Application instance
     * 
     * @var ApplicationInterface
     */
    protected $app;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->active = false;
        $this->app = null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function connect(ApplicationInterface $app)
    {
        $this->app = $app;
    }
    
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
    }
}