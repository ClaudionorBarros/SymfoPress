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
 * ContainerAwareInterface should be implemented by classes that depends on a Container.
 *
 * @author Carl Alexander <carlalexander@gmail.com>
 */
interface ContainerAwareInterface
{
    /**
     * Sets the Container.
     *
     * @param Container $container A Container instance
     */
    function setContainer(Container $container = null);
}