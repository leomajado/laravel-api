<?php

namespace App\Http\Traits;

use App\Models\User;

trait HelperTrait
{
  public function getJsonHeader()
  {
    return ['Content-Type: application/json'];
  }
}
