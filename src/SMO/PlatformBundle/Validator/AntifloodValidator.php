<?php
// src/SMO/PlatformBundle/Validator/AntifloodValidator.php

namespace SMO\PlatformBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntifloodValidator extends ConstraintValidator
{
    private $requestStack;
    private $em;
    
    public function __construct(RequestStack $requestStack, EntotyManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em           = $em;
    }
    
    public function validate($value, Constraint $constraint)
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request->getClentIp();
        
        $isFlood = $this->em
            ->getRepository('SMOPlatformBundle:Application')
            ->isFlood($ip, 15)
        ;
        
        if($isFlood)
        {
            $this->context->addViolation($constraint->message);
        }
    }
}