<?php

namespace SMO\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SMOUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
