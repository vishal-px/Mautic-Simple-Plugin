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
<<<<<<< HEAD
                'route'     => 'mautic_helloworld_world',
               	'iconClass' => 'fa-gear',
                'priority'  => 8
=======
                'id'        => 'plugin_helloworld_world',
		//'route'        => 'plugin_helloworld_world',
                'access'    => 'plugin:helloworld:worlds:view',
               	'parent'    => 'mautic.core.channels',
                'priority'  => 3
>>>>>>> 498558db52f545850be15199ad9aa0b17f01f748
            )
        )
    ),
	'categories' => array(
        'plugin:helloworld' => 'plugin.helloworld.world',
    ),
);
