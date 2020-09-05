<?php

namespace AW\DoliBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AWDoliBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSUserBundle';
  }
}
