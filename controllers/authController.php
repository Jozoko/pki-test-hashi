<?php
require './models/authModel.php';

class AuthController
{
    public function authenticate($username, $password): array
    {

        global $ldap_host, $ldap_port, $ldap_domain, $ldap_group;
        $authModel = new AuthModel();
        $response = [
            'success' => false,
            'error' => 'Ошибка: Неизвестная ошибка',
        ];

        if (empty($username) || empty($password)) {
            $response['error'] = 'Ошибка: Пожалуйста, введите логин и пароль';
            return $response;
        }

        $ldap_conn = ldap_connect($ldap_host, $ldap_port);
        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

        if (!$ldap_conn) {
            $response['error'] = 'Ошибка: Невозможно подключиться к серверу LDAP';
            return $response;
        }

        $ldap_bind = @ldap_bind($ldap_conn, $username . "@" . $ldap_domain, $password);

        if (!$ldap_bind) {
            $response['error'] = 'Ошибка: Неверный логин или пароль';
            return $response;
        }

        $userdn = $authModel->getUserDN($ldap_conn, $username);
        $groupdn = $authModel->getGroupDN($ldap_conn, $ldap_group);

        if ($userdn && $groupdn) {
            $auth = $authModel->checkGroupEx($ldap_conn, $userdn, $groupdn);

            if ($auth) {
                session_start();
                $_SESSION['authenticated'] = true;
                $_SESSION['username'] = $username;
                $response['success'] = true;
                $response['error'] = '';
            } else {
                $response['error'] = 'Ошибка: Пользователь или группа не найдены в AD';
            }
        } else {
            $response['error'] = 'Ошибка: Пользователь или группа не найдены в AD';
        }

        ldap_unbind($ldap_conn);
        return $response;
    }
}
