<?php
require 'config.php';

class AuthModel
{
    public function authenticate($username, $password): array
    {
        global $ldap_host, $ldap_port, $ldap_domain, $ldap_group;
        $response = [
            'success' => false,
            'error' => 'Ошибка: Неизвестная ошибка',
        ];

        if (empty($username) || empty($password)) {
            $response['error'] = 'Ошибка: Пожалуйста, введите логин и пароль';
            return $response;
        }

        $ldap_conn = $this->connectToLDAP($ldap_host, $ldap_port);
        if (!$ldap_conn) {
            $response['error'] = 'Ошибка: Невозможно подключиться к серверу LDAP';
            return $response;
        }

        $ldap_bind = @ldap_bind($ldap_conn, $username . "@" . $ldap_domain, $password);

        if (!$ldap_bind) {
            $response['error'] = 'Ошибка: Неверный логин или пароль';
            ldap_unbind($ldap_conn);
            return $response;
        }

        $userdn = $this->getUserDN($ldap_conn, $username);
        $groupdn = $this->getGroupDN($ldap_conn, $ldap_group);

        if ($userdn && $groupdn) {
            $auth = $this->checkGroupEx($ldap_conn, $userdn, $groupdn);

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
        return $response;
    }

    private function connectToLDAP($host, $port)
    {
        $ldap_conn = ldap_connect($host, $port);
        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
        return $ldap_conn;
    }

    public function getUserDN($ldap_conn, $username)
    {
        global $ldap_base_dn;
        $search = ldap_search($ldap_conn, $ldap_base_dn, "sAMAccountName={$username}");
        if ($search === false) {
            return null;
        }
        $result = ldap_get_entries($ldap_conn, $search);
        return $result[0]['dn'] ?? null;
    }

    public function getGroupDN($ldap_conn, $group)
    {
        global $ldap_base_dn;
        $search = ldap_search($ldap_conn, $ldap_base_dn, "cn={$group}");
        if ($search === false) {
            return null;
        }
        $result = ldap_get_entries($ldap_conn, $search);
        return $result[0]['dn'] ?? null;
    }

    public function checkGroupEx($ldap_conn, $userdn, $groupdn): bool
    {
        $search = ldap_search($ldap_conn, $userdn, "memberOf={$groupdn}", ['dn']);
        if ($search === false) {
            return false;
        }

        $result = ldap_get_entries($ldap_conn, $search);
        return isset($result[0]['dn']);
    }
}
