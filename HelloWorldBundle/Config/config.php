<?php
return array(
    'name'          => 'Hello World',
    'description'   => 'This is an example config file for a simple Hello World addon.',
    'version'       => '0.1.0',
    'routes'   => array(
        'main' => array(
            'plugin_helloworld_world' => array(
                'path'       => '/hello/{world}',
                'controller' => 'HelloWorldBundle:Default:world',
                'defaults'    => array(
                    'world' => 'earth'
                ),1
                'requirements' => array(
                    'world' => 'earth|mars'
                )
            ),
        ),
      ),
	'menu' => array(
     /* 'main' => array(
            'priority' => 8,
            'items'    => array(
                'plugin.helloworld.world' => array(
                    'id'        => 'plugin_helloworld_world',
                    'access'    => 'plugin:helloworld:worlds:view',
                    'parent'    => 'mautic.core.channels',
                )
            )
        ), */
	
	 'main' => array(
            'plugin.helloworld.world' => array(
                'id'        => 'plugin_helloworld_world',
		//'route'        => 'plugin_helloworld_world',
                'access'    => 'plugin:helloworld:worlds:view',
               	'parent'    => 'mautic.core.channels',
                'priority'  => 3
            )
        )
    ),
	'categories' => array(
        'plugin:helloworld' => 'plugin.helloworld.world',
    ),
);
