<?php
// Конфигурация
require_once 'config.php';

// Получение логина и пароля из формы
$username = $_POST['username'];
$password = $_POST['password'];

// Функция для проверки принадлежности пользователя к группе
function checkGroupMembership($ldap_conn, $user_dn, $group_dn): bool
{
    $result = ldap_read($ldap_conn, $user_dn, "(memberOf={$group_dn})");
    $entries = ldap_get_entries($ldap_conn, $result);
    return ($entries['count'] > 0);
}

// Функция для поиска DN пользователя
function getUserDN($ldap_conn, $base_dn, $username) {
    $result = ldap_search($ldap_conn, $base_dn, "(sAMAccountName={$username})");
    if ($result === false) {
        error_log("Ошибка поиска пользователя: " . ldap_error($ldap_conn));
    }

    $entries = ldap_get_entries($ldap_conn, $result);
    if ($entries['count'] > 0) {
        return $entries[0]['dn'];
    }

    error_log("Пользователь не найден: " . $username);
    return null;
}

function getGroupDN($ldap_conn, $base_dn, $group) {
    $result = ldap_search($ldap_conn, $base_dn, "(cn={$group})");
    if ($result === false) {
        error_log("Ошибка поиска группы: " . ldap_error($ldap_conn));
    }

    $entries = ldap_get_entries($ldap_conn, $result);
    if ($entries['count'] > 0) {
        return $entries[0]['dn'];
    }

    error_log("Группа не найдена: " . $group);
    return null;
}

// Подключение к AD
$ldap_conn = ldap_connect($ldap_host, $ldap_port);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

// Аутентификация
$bind = @ldap_bind($ldap_conn, $username . $domain, $password);
if ($bind) {
    $user_dn = getUserDN($ldap_conn, $base_dn, $username);
    $group_dn = getGroupDN($ldap_conn, $base_dn, $group);

    if ($user_dn) {
        $is_member = checkGroupMembership($ldap_conn, $user_dn, $group_dn);
        if ($is_member) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен: пользователь не принадлежит к указанной группе.']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Ошибка: пользователь или группа не найдены в AD']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Ошибка: Пользователь или пароль неверны']);;
}

// Закрытие соединения
ldap_unbind($ldap_conn);
