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

use SymfoPress\Component\Extension\ExtensionInterface;

/**
 * Application class interface
 *
 * @author Carl Alexander <carlalexander@gmail.com>
 */
interface ApplicationInterface
{    
    /**
     * Boots the application.
     */
    public function boot();
    
    /**
     * Gets the connected bundle instances.
     *
     * @return array An array of connected extension instances
     */
    public function getExtensions();
    
    /**
     * Returns an array of extension to registers.
     *
     * @return array An array of extension instances.
     */
    public function registerExtensions();
    
    /**
     * Mount all registered extensions to the application
     */
    public function mountExtensions();
    
    /**
     * Mounts an application extension.
     *
     * @param ExtensionInterface $extension An ExtensionInterface instance
     * @param array $values
     */
    public function mount(ExtensionInterface $extension, array $values);
}