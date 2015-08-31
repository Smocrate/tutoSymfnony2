<?php
// src/SMO/PlatformBundle/Antispam/SMOAntispam.php

namespace SMO\PlatformBundle\Antispam;

class SMOAntispam extends \Twig_Extension
{
    protected $mailer;
    protected $locale;
    protected $minLength;
    
    public function __construct(\Swift_Mailer $mailer, $minLength)
    {
        $this->mailer       = $mailer;
        $this->minLength    = (int) $minLength;
    }
    
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
    
    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }
    
    public function getFunctions()
    {
        return array(
            'checkIfSpam' => new \Twig_Function_Method($this, 'isSpam')
        );
    }
    
    public function getName()
    {
        return 'SMOAntispam';
    }
}