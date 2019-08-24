<?php

namespace ProjectBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProjectBundle extends Bundle
{
		public function getParent()
    {
        return 'FOSUserBundle';
    }
}
