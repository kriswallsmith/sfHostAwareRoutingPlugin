Example Usage
-------------

Using this plugin you can create a route that pulls values from the current
host:

    dashboard:
      url:   :username.%APP_HOST%/dashboard/:section
      class: sfHostAwareRoute
      param: { module: dashboard, action: showSection }

The token `%APP_HOST%` will be replaced with the return value of a call to
`sfConfig::get('app_host')`. You can interpolate any arbitrary config value
into the URL in this way.

Your `app.yml` entries might look something like this:

    prod:
      host: domain.com
    dev:
      host: domain.localhost

You can then use this route to generate URLs:

    <?php echo url_for('@dashboard', array(
      'username' => 'kriswallsmith',
      'section'  => 'account')) ?>

In the `dev` environment, this will output
`http://kriswallsmith.domain.localhost/dashboard/account`.

When the configured action is called, in this case `dashboard/showSection`,
both the `username` and `section` values extracted from the URL will be
available as request parameters:

    $request->getParameter('username'); // "kriswallsmith"
    $request->getParameter('section');  // "account"
