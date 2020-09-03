<?php
namespace kajimachi;

class Router
{
    private static $instance;

    private $routes;
    private $optionalRoutes;
    private $routeMethods;

    function __construct()
    {
        $this->route = [];
        $this->optionalRoutes = [];
        $this->routeMethods = [];

        $this->addRoute('GET', '/articles', 'ArticleController@articles');
        $this->addRoute('POST', '/article/post', 'ArticleController@post');
        $this->addRoute('POST', '/article/remove', 'ArticleController@remove');
        $this->addRoute('POST', '/article/update', 'ArticleController@update');
        $this->addRoute('GET', '/article/:id', 'ArticleController@show');

        $this->addRoute('POST', '/auth/login', 'AuthController@login');
        $this->addRoute('GET', '/auth/logout', 'AuthController@logout');

        if(DEBUG_MODE)
            $this->addRoute('GET', '/mng/db', 'InitializeController@createDatabase');
    }

    private function addRoute($method, $path, $func)
    {
        if(strpos($path, ':') === false)
        {
            $this->routes[$path] = $func;
        }
        else
        {
            $this->optionalRoutes[$path] = $func;
        }
        $this->routeMethods[$path] = $method;
    }

    public function route()
    {
        $urls = explode('?', $_SERVER['REQUEST_URI']);
        $url = $urls[0];

        if(isset($this->routes[$url]) === true && $_SERVER["REQUEST_METHOD"] === $this->routeMethods[$url])
        {
            $cls = explode('@', $this->routes[$url]);
            $fullname = 'kajimachi\\pages\\' . $cls[0];
            $inst = new $fullname();
            $inst->{$cls[1]}();
            exit;
        }

        //check optional routes
        foreach($this->optionalRoutes as $key => $val)
        {
            if($_SERVER["REQUEST_METHOD"] !== $this->routeMethods[$key])
                continue;

            $regex = preg_replace_callback('/:([^\/\s]+)/u', function($matches) {
                $r = str_replace(':', '', $matches[0]);
                return '(?P<' . $r . '>[^/\s]+)';
            }, $key);

            $regex = str_replace('/', '\\/', $regex);
            $regex = '/^'. $regex .'$/u';

            if(preg_match( $regex, $url, $match) === 1)
            {
                $cls = explode('@', $val);
                $fullname = 'kajimachi\\pages\\' . $cls[0];
                $inst = new $fullname();
                $inst->{$cls[1]}($match);
                exit;
            }
        }

        echo '404';
        exit;
    }

    public static function getInstance()
    {
        if(Router::$instance == null)
            Router::$instance = new Router();
        return Router::$instance;
    }
}


Router::getInstance()->route();

