<?php

/**
 * Adds interpolation of config values to some rules' url parameter.
 *
 * @package sfHostAwareRoutingPlugin
 * @author  Kris Wallsmith <kris.wallsmith@symfony-project.com>
 */
class sfHostAwareRoutingConfigHandler extends sfRoutingConfigHandler
{
  /**
   * @see sfRoutingConfigHandler
   */
  protected function parse($configFiles)
  {
    return array_map(array($this, 'filterRoute'), parent::parse($configFiles));
  }

  /**
   * Replaces config constants in the url for {@link sfHostAwareRoute} routes.
   *
   * @param array $route A parsed route array
   *
   * @return array The filter array
   */
  protected function filterRoute($route)
  {
    list($class, $args) = $route;

    if ('sfHostAwareRoute' == $class || is_subclass_of($class, 'sfHostAwareRoute'))
    {
      $args[0] = $this->replaceConstants($args[0]);
    }

    return array($class, $args);
  }
}
