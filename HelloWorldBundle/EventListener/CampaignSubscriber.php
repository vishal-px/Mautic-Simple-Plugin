<?php
namespace MauticPlugin\HelloWorldBundle\Events;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CampaignBundle\Event as Events;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;

/**
 * Class CampaignSubscriber
 */
class CampaignSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return array(
            CampaignEvents::CAMPAIGN_ON_BUILD => array('onCampaignBuild', 0),
            HelloWorldEvents::BLASTOFF        => array('executeCampaignAction', 0),
            HelloWorldEvents::VALIDATE_VISIT  => array('validateCampaignDecision', 0)
        );
    }

    /**
     * Add campaign decision and actions
     *
     * @param Events\CampaignBuilderEvent $event
     */
    public function onCampaignBuild(Events\CampaignBuilderEvent $event)
    {
        // Register custom action
        $event->addAction(
            'helloworld.send_offworld',
            array(
                'eventName'       => HelloWorldEvents::BLASTOFF,
                'label'           => 'plugin.helloworld.campaign.send_offworld',
                'description'     => 'plugin.helloworld.campaign.send_offworld_descr',
                // Set custom parameters to configure the decision
                'formType'        => 'helloworld_worlds',
                // Set a custom formTheme to customize the layout of elements in formType
                'formTheme'       => 'HelloWorldBundle:FormTheme\SubmitAction',
                // Set custom options to pass to the form type, if applicable
                'formTypeOptions' => array(
                    'world' => 'mars'
                )
            )
        );

        // Register custom decision (executes when a lead "makes a decision" i.e. executes some direct action
        $event->addDecision(
            'helloworld.visits_mars',
            array(
                'eventName'       => HelloWorldEvents::VALIDATE_VISIT,
                'label'           => 'plugin.helloworld.campaign.visits_mars',
                'description'     => 'plugin.helloworld.campaign.visits_mars_descr',
                // Same as registering an action
                'formType'        => false,
                'formTypeOptions' => array()
            )
        );
    }

    /**
     * Execute campaign action
     *
     * @param CampaignExecutionEvent $event
     */
    public function executeCampaignAction (CampaignExecutionEvent $event)
    {
        // Do blastoff

        $event->setResult(true);
    }

    /**
     * Validate campaign decision
     *
     * @param CampaignExecutionEvent $event
     */
    public function validateCampaignDecision (CampaignExecutionEvent $event)
    {
        $valid = ($event->getEventDetails()->getId() === $event->getConfig()['id']);
        $event->setResult($valid);
    }
}