<?php
    Class IhSession extends CI_Model
    {
        function refreshSession()
        {
            session_start();
            if (isset($_SESSION['SESS_TIMEOUT'])) {
                if ($_SERVER['REQUEST_TIME'] > $_SESSION['SESS_TIMEOUT']) {
                    setcookie(session_name(), session_id(), -1, '/');
                    session_unset();
                    session_destroy();
                    return 0;
                } else {
                    $_SESSION['SESS_TIMEOUT'] = $_SERVER['REQUEST_TIME'] + 604800;
                    return 1;
                }
            } else {
                $_SESSION['SESS_TIMEOUT'] = $_SERVER['REQUEST_TIME'] + 604800;
                return 1;
            }			
        }

        function clear_session()
        {
            setcookie(session_name(), session_id(), -1, '/');
            session_unset();
            session_destroy();
        }

        function start_session($expire = 0)
        {
            if ($expire == 0) {
                $expire = ini_get('session.gc_maxlifetime');
            } else {
                ini_set('session.gc_maxlifetime', $expire);
            }

            if (empty($_COOKIE['PHPSESSID'])) {
                session_set_cookie_params($expire);
                session_start();
            } else {
                session_start();
                setcookie('PHPSESSID', session_id(), time() + $expire, '/');
            }
        }
    }
?>