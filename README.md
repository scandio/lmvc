LMVC (cool)
====

Lean Model View Controller
------------------------------

This project contains a very small MVC framework written with simple PHP classes. It's not for production use but for learning purpose. It's developed with the following boundary conditions:

* Just PHP - no external PHP libraries
* Convention over configuration
* No annotations or other stuff like that
* No namespaces

Currently there is no documentation for anything.

How To
------

You have to change the .htaccess file if you want to try it

    RewriteRule ^(.*)$ /path/to/your/index.php?app-slug=$1 [L,QSA]

### Controllers and actions

    http:://host/base-path/controller/action/param1/param2

The URL above shows the controller & action with theirs params

    http:://host/base-path/

is a special controller and a special action. In this case the controller is named Application and the action index()

    http:://host/base_path/xyz

Here the controller is Xyz and the action is index()

    http:://host/base_path/xyz/do

Again the controller is Xyz and the action is do()

To develop your own controller create a file with the same name like the class in the controllers directory

    e.g. Accounts.php

Create a class as a descendant of class Controller

    class Accounts extends Controller { }

Create a public static method named like the action you want to call

    class Accounts extends Controller {

        public static function index() {
            print_r('ok');
        }

    }

Try to call

    http://host/base-path/accounts/

### Rendering views

Currently LMVC supports to rendering options. First the standard HTML rendering. Second the JSON output. The class Controller is having two static methods for that. All data that have to be passed to the template or to JSON must be set by setRenderArg().

Example for HTML rendering:

    class Accounts extends Controller {

        public static function index() {
            self::render()
        }

    }

This renders the the view (template) path/views/controller/action.html. In this case views/accounts/index.html. To pass some data to the template you can...

    class Accounts extends Controller {

        public static function index() {
            self::setRenderArg('name', 'John Doe');
            self::render()
        }

    }

or

    class Accounts extends Controller {

        public static function index() {
            self::render(array('name' => 'John Doe'));
        }

    }

or both. There is no specific template language. It's just PHP. Every render argument is accessible as a local variable.

    <h1>Hello <?= $name ?></h1>

Example for JSON rendering

    class Accounts extends Controller {

        public static function index() {
            self::renderJson(array('name' => 'John Doe'));
        }

    }

has an output like

    {"name": "John Doe"}











