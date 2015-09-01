<?php
// src/SMO/PlatformBundle/Beta/BetaListener.php

namespace SMO\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
    // Notre processeur
    protected $betaHTML;

    // La date de fin de la version b�ta :
    // - Avant cette date, on affichera un compte � rebours (J-3 par exemple)
    // - Apr�s cette date, on n'affichera plus le � b�ta �
    protected $endDate;

    public function __construct(BetaHTML $betaHTML, $endDate)
    {
        $this->betaHTML = $betaHTML;
        $this->endDate  = new \Datetime($endDate);
    }

    public function processBeta(FilterResponseEvent $event)
    {
        if(!$event->isMasterRequest())
        {
            return;
        }
        $remainingDays = $this->endDate->diff(new \Datetime())->format('%d');

        if ($remainingDays <= 0) {
            // Si la date est d�pass�e, on ne fait rien
            return;
        }

        $response = $this->betaHTML->displayBeta($event->getResponse(), $remainingDays );
        $event->setResponse($response);

        #$event->stopPropagation();
    }
}