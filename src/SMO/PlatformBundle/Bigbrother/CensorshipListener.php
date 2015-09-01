<?php
// src/SMO/PlatformBundle/Bigbrother/CensorshipListener.php

namespace SMO\PlatformBundle\Bigbrother;


class CensorshipListener
{
    protected $processor;
    protected $listUsers = array();

    public function __construct(CensorshipProcessor $processor, $listUsers)
    {
        $this->processor = $processor;
        $this->listUsers = $listUsers;
    }

    public function processMessage(MessagePostEvent $event)
    {
        if(in_array($event->getUser()->getId(), $this->listUsers))
        {
            $this->processor->notifyEmail($event->getMessage(), $event->getUser());

            $message = $this->processor->censorMessage($event->getMessage());

            $event->setMessage($message);
        }


    }
}