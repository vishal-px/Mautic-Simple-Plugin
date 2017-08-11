<?php
// plugins/HelloWorldBundle/Controller/DefaultController.php

namespace MauticPlugin\HelloWorldBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;

class DefaultController extends FormController
{
    /**
     * Display the world view
     *
     * @param string $world
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function worldAction($world = 'earth')
    {
        /** @var \MauticPlugin\HelloBundleBundle\Model\WorldModel $model */
        //$model = $this->getModel('helloworld.world');

        // Retrieve details about the world
        //$worldDetails = $model->getWorldDetails($world);
	        return $this->delegateView(
            array(
                'viewParameters'  => array(
                    'world'   => $world,
                    //'details' => $worldDetails
                ),
                'contentTemplate' => 'HelloWorldBundle:World:index.html.php',
                'passthroughVars' => array(
                    'activeLink'    => 'mautic_helloworld_world',
                    'route'         => $this->generateUrl('mautic_helloworld_world', array('world' => $world)),
                    'mauticContent' => 'helloWorldDetails'
                )
            )
        );
    }
}
