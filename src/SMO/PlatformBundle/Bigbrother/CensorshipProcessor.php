<?php
// src/SMO/PlatformBundle/Bigbrother/CensorshipProcessor.php

namespace SMO\PlatformBundle\Bigbrother;


use SMO\UserBundle\Entity\User;

class CensorshipProcessor
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyEmail($message, User $user)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject("Nouveau message d'un utilisateur surveillé")
            ->setFrom('guillaume.b.estrade@gmail.com')
            ->setTo('guillaume.b.estrade@gmail.com')
            ->setBody("L'utilisateur surveillé ".$user->getUsername()." a posté le message suivant '".$message."'")
        ;

        $this->mailer->send($message);
    }

    public function censorMessage($message)
    {
        $message = str_replace(array('top secret','mot interdit','lol'), '', $message);

        return $message;
    }
}