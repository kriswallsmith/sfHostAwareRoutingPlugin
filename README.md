Example Usage
-------------

Using this plugin you can create a route that pulls values from the current
host:

    dashboard:
      url: :username.mydomain.com/dashboard/:section
      class: sfHostAwareRoute
      param: { module: dashboard, action: showSection }

Todo
----

Portions of the host need to be hard-coded at the moment. I'm going to add a
config handler that interpolates arbitrary config values into `url`, such as
`%MY_BASE_HOST%`.
