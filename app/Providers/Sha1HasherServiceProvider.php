<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Sha1Hasher;

class Sha1HasherServiceProvider extends ServiceProvider
{

  protected $defer = true;

  public function register()
  {
    $this->app->singleton('hash', function () {
      return new Sha1Hasher;
    });
  }

  public function provides()
  {
    return ['hash'];
  }
}
