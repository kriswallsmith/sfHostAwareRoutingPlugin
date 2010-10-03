<?php

require_once dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php';
$configuration = new ProjectConfiguration();

$autoload = sfSimpleAutoload::getInstance();
$autoload->addDirectory(dirname(__FILE__).'/../lib');
$autoload->register();

class sfHostAwareRouteTest extends PHPUnit_Framework_TestCase
{
  public function testHostRoute()
  {
    $route = new sfHostAwareRoute(':username.localhost/dashboard/:section', array(
      'module' => 'dashboard',
      'action' => 'showSection',
    ));

    $this->assertInstanceOf('sfRoute', $route->getHostRoute());

    return $route;
  }

  /**
   * @depends testHostRoute
   */
  public function testMatchesUrl($route)
  {
    $parameters = $route->matchesUrl('/dashboard/account', array(
      'host'   => 'pierre.localhost',
      'method' => 'get',
    ));

    $this->assertEquals(array(
      'module'   => 'dashboard',
      'action'   => 'showSection',
      'username' => 'pierre',
      'section'  => 'account',
    ), $parameters);
  }

  /**
   * @depends testHostRoute
   */
  public function testGenerate($route)
  {
    $parameters = array('username' => 'pierre', 'section' => 'account');

    $this->assertEquals('http://pierre.localhost/dashboard/account', $route->generate($parameters));
  }

  /**
   * @depends testHostRoute
   */
  public function testGenerateAbsolute($route)
  {
    $parameters = array('username' => 'pierre', 'section' => 'account');
    $context = array();
    $absolute = true;

    $this->assertEquals('http://pierre.localhost/dashboard/account', $route->generate($parameters, $context, $absolute));
  }

  /**
   * @depends testHostRoute
   */
  public function testGenerateSecure($route)
  {
    $parameters = array('username' => 'pierre', 'section' => 'account');
    $context = array('is_secure' => true);

    $this->assertEquals('https://pierre.localhost/dashboard/account', $route->generate($parameters, $context));
  }

  /**
   * @depends testHostRoute
   */
  public function testGeneratePrefix($route)
  {
    $parameters = array('username' => 'pierre', 'section' => 'account');
    $context = array('prefix' => '/frontend_dev.php');

    $this->assertEquals('http://pierre.localhost/frontend_dev.php/dashboard/account', $route->generate($parameters, $context));
  }

  /**
   * @depends testHostRoute
   */
  public function testMatchesParameters($route)
  {
    $this->assertTrue($route->matchesParameters(array(
      'module'   => 'dashboard',
      'action'   => 'showSection',
      'username' => 'pierre',
      'section'  => 'account',
    )));

    $this->assertFalse($route->matchesParameters(array(
      'module'   => 'dashboard',
      'action'   => 'showSection',
      'section'  => 'account',
    )));

    $this->assertFalse($route->matchesParameters(array(
      'module'   => 'dashboard',
      'action'   => 'showSection',
      'username' => 'pierre',
    )));
  }

  public function testNoHostRoute()
  {
    $route = new sfHostAwareRoute('/dashboard/:section', array(
      'module' => 'dashboard',
      'action' => 'showSection',
    ));

    $this->assertSame(null, $route->getHostRoute());

    return $route;
  }

  /**
   * @depends testNoHostRoute
   */
  public function testNoHostMatchesUrl($route)
  {
    $parameters = $route->matchesUrl('/dashboard/account', array('method' => 'get'));

    $this->assertEquals(array(
      'module'  => 'dashboard',
      'action'  => 'showSection',
      'section' => 'account',
    ), $parameters);
  }

  /**
   * @depends testNoHostRoute
   */
  public function testNoHostGenerate($route)
  {
    $parameters = array('section' => 'account');

    $this->assertEquals('/dashboard/account', $route->generate($parameters));
  }

  /**
   * @depends testNoHostRoute
   */
  public function testNoHostMatchesParameters($route)
  {
    $this->assertTrue($route->matchesParameters(array(
      'module'   => 'dashboard',
      'action'   => 'showSection',
      'section'  => 'account',
    )));

    $this->assertFalse($route->matchesParameters(array(
      'module' => 'dashboard',
      'action' => 'showSection',
    )));
  }

  /**
   * @depends testHostRoute
   */
  public function testSerialization($route)
  {
    $serialized = serialize($route);
    unset($route);

    $route = unserialize($serialized);

    $this->assertInstanceOf('sfRoute', $route->getHostRoute());
  }
}
