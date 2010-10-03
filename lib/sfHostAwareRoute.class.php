<?php

/**
 * Represents a host-aware route.
 * 
 * Example usage:
 * 
 *     dashboard:
 *       url:   :username.domain.com/dashboard/:section
 *       class: sfHostAwareRoute
 *       param: { module: dashboard, action: showSection }
 * 
 * @todo Interpolation of config values into url via a custom config handler
 */
class sfHostAwareRoute extends sfRequestRoute
{
  protected
    $hostRoute = null;

  public function __construct($pattern, array $defaults = array(), array $requirements = array(), array $options = array())
  {
    if ('/' != $pattern[0])
    {
      list($host, $pattern) = explode('/', $pattern, 2);

      $this->hostRoute = $this->createHostRoute($host, $defaults, $requirements, $options);
    }

    parent::__construct($pattern, $defaults, $requirements, $options);
  }

  public function matchesUrl($url, $context = array())
  {
    if (false === $parameters = parent::matchesUrl($url, $context))
    {
      // uri does not match
      return false;
    }

    if ($this->hostRoute)
    {
      if (false === $hostParameters = $this->hostRoute->matchesUrl('/'.$context['host'], $context))
      {
        // host does not match
        return false;
      }

      $parameters += $hostParameters;
    }

    return $parameters;
  }

  public function matchesParameters($params, $context = array())
  {
    if (!$this->hostRoute)
    {
      return parent::matchesParameters($params, $context);
    }

    $hostParams = $this->extractHostParams($params);

    return parent::matchesParameters($params, $context) && $this->hostRoute->matchesParameters($hostParams, $context);
  }

  public function generate($params, $context = array(), $absolute = false)
  {
    if (!$this->hostRoute)
    {
      return parent::generate($params, $context, $absolute);
    }

    $hostParams = $this->extractHostParams($params);

    $protocol = isset($context['is_secure']) && $context['is_secure'] ? 'https' : 'http';
    $host = $this->hostRoute->generate($hostParams, $context, false);
    $prefix = isset($context['prefix']) ? $context['prefix'] : '';
    $uri = parent::generate($params, $context, false);

    return $protocol.':/'.$host.$prefix.$uri;
  }

  protected function createHostRoute($pattern, $defaults, $requirements, $options)
  {
    $filteredDefaults = array();
    $filteredRequirements = array();

    // this temporary route is just for extracting variables from the pattern
    $tmp = new sfRoute($pattern);

    foreach (array_keys($tmp->getVariables()) as $name)
    {
      if (isset($defaults[$name]))
      {
        $filteredDefaults[$name] = $defaults[$name];
      }

      if (isset($requirements[$name]))
      {
        $filteredRequirements[$name] = $requirements[$name];
      }
    }

    return new sfRoute($pattern, $filteredDefaults, $filteredRequirements, $options);
  }

  protected function extractHostParams(& $params)
  {
    $hostParams = array();
    foreach (array_keys($this->hostRoute->getVariables()) as $name)
    {
      if (isset($params[$name]))
      {
        $hostParams[$name] = $params[$name];
        unset($params[$name]);
      }
    }

    return $hostParams;
  }
}
