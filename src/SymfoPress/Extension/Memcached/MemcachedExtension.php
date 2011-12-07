<?php

/*
 * This file is part of the SymfoPress framework.
 * 
 * (c) Carl Alexander <carlalexander@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfoPress\Extension\Memcached;

use SymfoPress\Bridge\WordPress\Memcached\Manager;
use SymfoPress\Bridge\WordPress\Memcached\WordPressDB;
use SymfoPress\Component\Application\ApplicationInterface;
use SymfoPress\Component\Extension\Extension;

/**
 * WordPress memcached extension
 *
 * @author Carl Alexander <carlalexander@gmail.com>
 */
class MemcachedExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function connect(ApplicationInterface $app)
    {
        $this->app = $app;
        
        if (!isset($app['memcachedhost']) || !isset($app['memcachedport'])) {
            return;
        }

        $app['memcached'] = $app->share(function () use ($app) {
            return new Manager($app['memcachedhost'], $app['memcachedport'], $tapp['dbname']);
        });

        $app['wordpressdb'] = $app->share(function () use ($app) {
            return new WordPressDB($app['dbuser'], $app['dbpassword'], $app['dbname'], $app['dbhost'], $app['memcached']);
        });
    }
}