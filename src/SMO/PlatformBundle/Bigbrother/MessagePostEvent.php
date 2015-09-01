<?php
// src/SMO/PlatformBundle/Bigbrother/MessagePostEvent.php

namespace SMO\PlatformBundle\Bigbrother;


use SMO\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class MessagePostEvent extends Event
{
    protected $message;
    protected $user;

    public function __construct($message, User $user)
    {
        $this->message  = $message;
        $this->user     = $user;
    }

    public function getMessage()
    {
        return $this->message . " Avec un message piraté !";
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getUser()
    {
        return $this->user;
    }
}