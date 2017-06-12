<?php
session_name('small');
session_start();
date_default_timezone_set('America/Sao_Paulo');


function start()
{
    $controller = 'Index';
    $action = 'index';
    $params = array();

    $uri = $_SERVER['REQUEST_URI'];
    $request = explode('/', ltrim($uri, '/'));
    if (isset($request[0])) {
        if ($request[0] !== '') {
            $controller = ucfirst($request[0]);
        }
    }

    if (isset($request[1])) {
        if ($request[1] !== '') {
            $action = lcfirst($request[1]);
        }
    }

    if (isset($request[2])) {
        if ($request[2] !== '') {
            $get = explode('&', $request[2]);

            for ($i = 0; $i < count($get); $i++) {
                $param = explode("=", $get[$i]);
                $params[$param[0]] = $param[1];
            }
        }
    }

    $filename = 'controllers/' . $controller . '.php';
    if (!file_exists($filename)) {
        die("Controller não encontrado - $controller");
    }

    require_once $filename;
    $instance = new $controller($params);

    if (!method_exists($instance, $action)) {
        die("Action não encontrada - {$controller}->{$action}");
    }

    $instance->$action();
}

function getPost($name, $default = '')
{
    return (isset($_POST[$name])) ? trim($_POST[$name]) : $default;
}

function getSession($name, $default = null)
{
    return (isset($_SESSION[$name])) ? trim($_SESSION[$name]) : $default;
}


/**
 *
 * @param type date
 * @param type int 1 para formato brasileiro 2 para americano
 * formata data
 */
function formatDate($data, $local)
{
    if ($local == 2) {
        return join("-", (array_reverse(explode('/', $data))));
    }
    if ($local == 1) {
        return join("/", (array_reverse(explode('-', $data))));
    }
}


function emailValidate($email)
{
    //verifica se e-mail esta no formato correto de escrita
    if (!preg_match('#^([a-zA-Z0-9.-_])*([@])([a-z0-9]*).([a-z]{2,3})#', $email)) {
        return false;
    }
    return true;
}

function senha($senha)
{
    return md5('small' . $senha);
}
function my_autoload($pClassName)
{
    $class = explode("_", $pClassName);
    switch ($class[0]) {
        case "Model":
            unset($class[0]);
            $class = join(DIRECTORY_SEPARATOR, $class);
            require_once("models" . DIRECTORY_SEPARATOR . $class . ".php");
            break;
        default:
            require_once("controllers/" . $pClassName . ".php");
            break;
    }
}

spl_autoload_register("my_autoload");



