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
 * Interface for reusable application extensions.
 * 
 * @author Carl Alexander <carlalexander@gmail.com>
 */
interface ExtensionInterface
{
    /**
     * Connect extension to the application.
     * 
     * @param ApplicationInterface $app An ApplicationInterface instance
     */
    public function connect(ApplicationInterface $app);
    
    /**
     * Boots the extension.
     */
    public function boot();
}