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
 * A simple implementation of ContainerAwareInterface.
 *
 * @author Carl Alexander <carlalexander@gmail.com>
 */
class ContainerAware implements ContainerAwareInterface
{
    /**
     * @var Container
     */
    protected $container;
    
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * Sets the Container.
     *
     * @param Container $container A Container instance
     */
    function setContainer(Container $container = null)
    {
        $this->container = $container;
    }
}