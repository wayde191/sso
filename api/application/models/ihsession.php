<?php
    Class Ihsession extends CI_Model
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
    }
?>