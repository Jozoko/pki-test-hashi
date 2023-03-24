<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];
function getUserDN($ldap_conn, $username)
{
    global $ldap_base_dn;
    $search = ldap_search($ldap_conn, $ldap_base_dn, "sAMAccountName={$username}");
    if ($search === false) {
        return null;
    }
    $result = ldap_get_entries($ldap_conn, $search);
    return $result[0]['dn'] ?? null;
}

function getGroupDN($ldap_conn, $group)
{
    global $ldap_base_dn;
    $search = ldap_search($ldap_conn, $ldap_base_dn, "cn={$group}");
    if ($search === false) {
        return null;
    }
    $result = ldap_get_entries($ldap_conn, $search);
    return $result[0]['dn'] ?? null;
}
function checkGroupEx($ldap_conn, $userdn, $groupdn): bool
{
    $search = ldap_search($ldap_conn, $userdn, "memberOf={$groupdn}", ['dn']);
    if ($search === false) {
        return false;
    }

    $result = ldap_get_entries($ldap_conn, $search);
    return isset($result[0]['dn']);
}

header('Content-Type: application/json');

$response = [
    'success' => false,
    'error' => 'Ошибка: Неизвестная ошибка',
];

if (empty($username) || empty($password)) {
    $response['error'] = 'Ошибка: Пожалуйста, введите логин и пароль';
    echo json_encode($response);
    exit;
}

$ldap_conn = ldap_connect($ldap_host, $ldap_port);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

if (!$ldap_conn) {
    $response['error'] = 'Ошибка: Невозможно подключиться к серверу LDAP';
    echo json_encode($response);
    exit;
}

$ldap_bind = @ldap_bind($ldap_conn, $username . "@" . $ldap_domain, $password);

if (!$ldap_bind) {
    $response['error'] = 'Ошибка: Неверный логин или пароль';
    echo json_encode($response);
    exit;
}

$userdn = getUserDN($ldap_conn, $username);
$groupdn = getGroupDN($ldap_conn, $ldap_group);

if ($userdn && $groupdn) {
    $auth = checkGroupEx($ldap_conn, $userdn, $groupdn);

    if ($auth) {
        $response['success'] = true;
        $response['error'] = '';
    } else {
        $response['error'] = 'Ошибка: Пользователь или группа не найдены в AD';
    }
} else {
    $response['error'] = 'Ошибка: Пользователь или группа не найдены в AD';
}

ldap_unbind($ldap_conn);
echo json_encode($response);
