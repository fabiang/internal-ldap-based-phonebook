<?php
/**
 * Bit mask for user roles
 */
if (!defined('CAKE_LDAP_TEST_USER_ROLE_USER')) {
    define('CAKE_LDAP_TEST_USER_ROLE_USER', 1);
}

if (!defined('CAKE_LDAP_TEST_USER_ROLE_EXTENDED')) {
    define('CAKE_LDAP_TEST_USER_ROLE_EXTENDED', 2);
}

if (!defined('CAKE_LDAP_TEST_USER_ROLE_ADMIN')) {
    define('CAKE_LDAP_TEST_USER_ROLE_ADMIN', 4);
}
