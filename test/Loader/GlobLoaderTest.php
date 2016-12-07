<?php
namespace Bricks\Migration\UnitTest\Loader;

use Bricks\Migration\Loader\GlobLoader;

/**
 * @author Artur Sh. Mamedbekov
 */
class GlobLoaderTest extends \PHPUnit_Framework_TestCase{
  public function testConstruct(){
    $loader = new GlobLoader(__NAMESPACE__ . '\Migrations', __DIR__ . '/Migrations');

    $this->assertEquals([new Migrations\M1, new Migrations\Migration2], $loader->getArrayCopy());
  }

  public function testConstruct_shouldUserGlobPattern(){
    $loader = new GlobLoader(__NAMESPACE__ . '\Migrations', __DIR__ . '/Migrations', 'Migration*.php');

    $this->assertEquals([new Migrations\Migration2], $loader->getArrayCopy());
  }
}
