<?php

/*
 * This file is part of the SymfoPress framework.
 * 
 * (c) Carl Alexander <carlalexander@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfoPress\Extension\Twig;

use SymfoPress\Component\Application\ApplicationInterface;
use SymfoPress\Component\Extension\Extension;

/**
 * Twig extension
 *
 * @author Carl Alexander <carlalexander@gmail.com>
 */
class TwigExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function connect(ApplicationInterface $app)
    {
        $this->app = $app;

        $app['twig'] = $app->share(function () use ($app) {
            $app['twig.options'] = array_replace(
                array(
                    'charset'          => $app['charset'],
                    'debug'            => $app['debug'],
                    'strict_variables' => $app['debug'],
                ),
                isset($app['twig.options']) ? $app['twig.options'] : array()
            );

            $twig = new \Twig_Environment($app['twig.loader'], $app['twig.options']);
            $twig->addGlobal('app', $app);

            if (isset($app['twig.configure'])) {
                $app['twig.configure']($twig);
            }

            return $twig;
        });
        
        $app['twig.loader.filesystem'] = $app->share(function () use ($app) {
            return new \Twig_Loader_Filesystem(isset($app['twig.path']) ? $app['twig.path'] : array());
        });

        $app['twig.loader.array'] = $app->share(function () use ($app) {
            return new \Twig_Loader_Array(isset($app['twig.templates']) ? $app['twig.templates'] : array());
        });

        $app['twig.loader'] = $app->share(function () use ($app) {
            return new \Twig_Loader_Chain(array(
                $app['twig.loader.filesystem'],
                $app['twig.loader.array'],
            ));
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (isset($this->app['symfony_bridges'])) {
            if (isset($this->app['url_generator'])) {
                $this->app['twig']->addExtension(new TwigRoutingExtension($app['url_generator']));
            }

            if (isset($this->app['translator'])) {
                $this->app['twig']->addExtension(new TwigTranslationExtension($app['translator']));
            }

            if (isset($this->app['form.factory'])) {
                $this->app['twig']->addExtension(new TwigFormExtension(array('form_div_layout.html.twig')));
            }
        }
    }
}