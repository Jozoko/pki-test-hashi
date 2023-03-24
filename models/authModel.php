<?php
require 'config.php';

class AuthModel
{
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
