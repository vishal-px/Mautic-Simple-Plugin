<?php
// plugins\HelloWorldBundle\Integration\MarsIntegration

namespace MauticPlugin\HelloWorldBundle\Integration;

use Mautic\PluginBundle\Entity\Integration;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Mautic\PluginBundle\Helper\oAuthHelper;

/**
 * Class MarsIntegration
 */
abstract class MarsIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'helloworld';
    }

    public function getDisplayName()
    {
        return 'Hello World';
    }
    
    public function getAuthenticationType()
    {
        return 'none';
    }

}