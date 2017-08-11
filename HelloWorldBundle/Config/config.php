<?php
return array(
    'name'          => 'Hello World',
    'description'   => 'This is an example config file for a simple Hello World addon.',
    'version'       => '0.1.0',
    'routes'   => array(
        'main' => array(
            'mautic_helloworld_world' => array(
                'path'       => '/hello/{world}',
                'controller' => 'HelloWorldBundle:Default:world',
                'defaults'    => array(
                    'world' => 'earth'
                ),
                'requirements' => array(
                    'world' => 'earth|mars'
                )
            ),
        ),
      ),
	'menu' => array(	
	 'main' => array(
            'plugin.helloworld.world' => array(
                'route'     => 'mautic_helloworld_world',
               	'iconClass' => 'fa-gear',
                'priority'  => 8
            )
        )
    ),
	'categories' => array(
        'plugin:helloworld' => 'plugin.helloworld.world',
    ),
);
