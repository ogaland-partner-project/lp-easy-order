<?php

namespace App\Services;

class Sha1Hasher implements \Illuminate\Contracts\Hashing\Hasher
{

  public function info($hashedValue)
  {
    return false;
  }

  public function make($value, array $options = [])
  {
    $wkData = sha1($value, true);

    return base64_encode($wkData);
  }

  public function check($value, $hashedValue, array $options = [])
  {
    return (base64_encode(sha1($value, true)) == $hashedValue);
  }

  public function needsRehash($hashedValue, array $options = [])
  {
    return false;
  }
}