<?php

namespace OudyPlat;

class Application {
    /**
     *
     * @var \OudyPlat\Page 
     */
    public $page = null;
    /**
     *
     * @var \OudyPlat\Session 
     */
    public $session = null;
    public $template = null;
    public function __construct() {
        $this->session = ($handler = Session::getHandler()) ? new $handler() : new Session();
        $this->template = new Template();
    }
    /**
     * 
     * @param \OudyPlat\Page $page
     * @return boolean
     */
    public function load($page) {
        $session = $this->session;
        $page->template = $this->template->forPage($page);
        $data = $page->data;
        $load = null;
        if(file_exists($controller = COMPONENTS_PATH.'system/controller.php'))
            $load = include($controller);
        else if(defined('PARENT_COMPONENTS_PATH') && file_exists($controller = PARENT_COMPONENTS_PATH.'system/controller.php'))
            $load = include($controller);
        if($load !== 1)
            return 0;
        $notyet = false;
        if(file_exists($controller = COMPONENTS_PATH.$page->component.'/controller.php'))
            $load = include($controller);
        else if(defined('PARENT_COMPONENTS_PATH') && file_exists($controller = PARENT_COMPONENTS_PATH.$page->component.'/controller.php'))
            $load = include($controller);
        else
            $notyet = true;
        if(file_exists($controller = COMPONENTS_PATH.$page->component.'/controllers/'.$page->task.'.php'))
            $load = include($controller);
        else if(defined('PARENT_COMPONENTS_PATH') && file_exists($controller = PARENT_COMPONENTS_PATH.$page->component.'/controllers/'.$page->task.'.php'))
            $load = include($controller);
        else if($notyet)
            return $this->error(2500);
        $this->page = clone $page;
    }
    /**
     * 
     * @param string $component
     * @param string $task
     * @param array|object $data
     * @return boolean
     */
    public function loadBy($component, $task = null, $data = null) {
        return $this->load(new Page(array(
            'component'=>   $component,
            'task'=>        $task,
            'data'=>        $data
        )));
    }
    /**
     * 
     * @param int $code
     * @param array|object $data
     * @return boolean
     */
    public function error($code = 404, $data = null) {
        return $this->loadBy('error', $code, $data);
    }
    public function loadByPageURL($url = null) {
        if(is_null($url)) {
            $url = new URL();
            $url->loadCurrentURL();
        }
        $page = new Page();
        $page->loadByPageURL($url);
        return $this->load($page);
    }
    /**
     * 
     * @param string $header
     */
    public function setHeader($header) {
        $headers = array (
            100 => 'HTTP/1.1 100 Continue',
            101 => 'HTTP/1.1 101 Switching Protocols',
            200 => 'HTTP/1.1 200 OK',
            201 => 'HTTP/1.1 201 Created',
            202 => 'HTTP/1.1 202 Accepted',
            203 => 'HTTP/1.1 203 Non-Authoritative Information',
            204 => 'HTTP/1.1 204 No Content',
            205 => 'HTTP/1.1 205 Reset Content',
            206 => 'HTTP/1.1 206 Partial Content',
            300 => 'HTTP/1.1 300 Multiple Choices',
            301 => 'HTTP/1.1 301 Moved Permanently',
            302 => 'HTTP/1.1 302 Found',
            303 => 'HTTP/1.1 303 See Other',
            304 => 'HTTP/1.1 304 Not Modified',
            305 => 'HTTP/1.1 305 Use Proxy',
            307 => 'HTTP/1.1 307 Temporary Redirect',
            400 => 'HTTP/1.1 400 Bad Request',
            401 => 'HTTP/1.1 401 Unauthorized',
            402 => 'HTTP/1.1 402 Payment Required',
            403 => 'HTTP/1.1 403 Forbidden',
            404 => 'HTTP/1.1 404 Not Found',
            405 => 'HTTP/1.1 405 Method Not Allowed',
            406 => 'HTTP/1.1 406 Not Acceptable',
            407 => 'HTTP/1.1 407 Proxy Authentication Required',
            408 => 'HTTP/1.1 408 Request Time-out',
            409 => 'HTTP/1.1 409 Conflict',
            410 => 'HTTP/1.1 410 Gone',
            411 => 'HTTP/1.1 411 Length Required',
            412 => 'HTTP/1.1 412 Precondition Failed',
            413 => 'HTTP/1.1 413 Request Entity Too Large',
            414 => 'HTTP/1.1 414 Request-URI Too Large',
            415 => 'HTTP/1.1 415 Unsupported Media Type',
            416 => 'HTTP/1.1 416 Requested range not satisfiable',
            417 => 'HTTP/1.1 417 Expectation Failed',
            500 => 'HTTP/1.1 500 Internal Server Error',
            501 => 'HTTP/1.1 501 Not Implemented',
            502 => 'HTTP/1.1 502 Bad Gateway',
            503 => 'HTTP/1.1 503 Service Unavailable',
            504 => 'HTTP/1.1 504 Gateway Time-out',
            'html' => 'Content-type: text/html; charset=utf-8',
            'json' => 'Content-type: application/json; charset=utf-8',
            'xml' => 'Content-type: application/xml; charset=utf-8',
            'oudyplat' => 'X-Powered-By: OudyPlat 2.5'
        );
        header(isset($headers[$header]) ? $headers[$header] : $header);
    }
    public function render($module = 'layout', $position = null) {
        $session = $this->session;
        $page = $this->page;
        $data = $page->data;
        $template = $page->template;
        switch($module) {
            case 'layout':
                include TEMPLATES_PATH.$template->name.'/layout/'.$template->layout.'.php';
                break;
        }
    }
}