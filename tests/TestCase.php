<?php

namespace berthott\Crudable\Tests;

use berthott\Crudable\CrudableServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

  public function setUp(): void 
  {
    parent::setUp();
  }

  protected function getPackageProviders($app)
  {
    return [
      CrudableServiceProvider::class
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    $this->setUpUserTable();
    Config::set('crudable.namespace', 'berthott\Crudable\Tests');
  }

  private function setUpUserTable(): void 
  {
    Schema::create('users', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('firstname');
      $table->string('lastname');
      $table->timestamps();
    });
  }
}