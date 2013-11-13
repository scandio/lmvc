<?php

namespace Scandio\lmvc;

class PHPRenderer implements RendererInterface
{
    /**
     * Renders a HTML view. It loads the corresponding view automatically
     * The naming convention for a view is Application::index() opens views/application/index.html
     * and views/main.html have to exists as master template. You can overwrite
     * template and master template with optional parameters. The method extracts all elements
     * of $renderArgs to local variables which may be used in the template
     *
     * @static
     * @param array $renderArgs optional an associative array of values
     * @param string $template optional a file name like 'views/test/test.html' which overwrites the default
     * @param int $httpCode
     * @param string $masterTemplate optional a file name like 'views/test/test.html' which overwrites the default master
     * @return bool
     */
    public static function render($renderArgs = array(), $template = null, $httpCode = 200, $masterTemplate = null)
    {
        http_response_code($httpCode);
        extract($renderArgs);
        $app = LVC::get();
        if ($template) {
            $app->view = $app->config->appPath . $template;
        } else {
            $app->view = self::searchView(LVC::camelCaseTo($app->controller) . '/' . LVC::camelCaseTo($app->actionName) . '.html');
        }
        if (!is_null($masterTemplate)) {
            $masterTemplate = $app->config->appPath . $masterTemplate;
        } else {
            $masterTemplate = self::searchView('main.html');
        }
        include($masterTemplate);
        return true;
    }

    /**
     * searches for the view in the registered directories
     *
     * @static
     * @param $view
     * @return string|bool either the view's full path or false
     */
    private static function searchView($view)
    {
        $config = LVC::get()->config;
        foreach ($config->viewPath as $path) {
            $viewPath = ((substr($path, 0, 1) == '/') ? '' : $config->appPath) . $path . '/' . $view;
            if (file_exists($viewPath)) {
                return $viewPath;
            }
        }
        return false;
    }
}