<?php

namespace utils;

class WAF
{

    private $IPHeader;
    private $CookieCheck;
    private $CookieCheckParam;

    /**
     * Costruttore dell'oggetto WAF
     */
    function __construct()
    {
        $this->IPHeader = "REMOTE_ADDR";            // Imposta l'Header per ottenere l'IP del client,
        $this->CookieCheck = true;                  // per il controllo dei cookies (vedi funzione cookieCheck)
        $this->CookieCheckParam = 'username';       // e i parametri da controllare salvati nel cookie

        // return true;
    }

    /**
     *  display a view for blocking requests
     *  @todo
     */
    function vulnDetectedHTML($Method, $BadWord, $DisplayName, $TypeVuln)
    {
        header('HTTP/1.0 403 Forbidden');
        die( include('./views/denyPage.php') );          // Blocca la richiesta e mostra la View
    }

    /**
     * Ottiene un array di valori usati per le iniezioni di SQL o XSS
     * 
     * @param String $Type Il tipo di iniezione da cui ricavare i valori
     * 
     * @return Array:Bool Gli array di valori malevoli oppure false nel caso non fosse XSS o SQLi
     */
    function getArray($type)
    {
        switch ($type) {
            case 'SQL':                 // Iniezioni SQL
                return array(
                    "'",
                    '´',
                    'SELECT FROM',
                    'SELECT * FROM',
                    'ONION',
                    'union',
                    'UNION',
                    'UDPATE users SET',
                    'WHERE username',
                    'DROP TABLE',
                    '0x50',
                    'mid((select',
                    'union(((((((',
                    'concat(0x',
                    'concat(',
                    'OR boolean',
                    'or HAVING',
                    "OR '1", # Famous skid Poc. 
                    '0x3c62723e3c62723e3c62723e',
                    '0x3c696d67207372633d22',
                    '+#1q%0AuNiOn all#qa%0A#%0AsEleCt',
                    'unhex(hex(Concat(',
                    'Table_schema,0x3e,',
                    '0x00', // \0  [This is a zero, not the letter O]
                    '0x08', // \b
                    '0x09', // \t
                    '0x0a', // \n
                    '0x0d', // \r
                    '0x1a', // \Z
                    '0x22', // \"
                    '0x25', // \%
                    '0x27', // \'
                    '0x5c', // \\
                    '0x5f'  // \_
                );

                break;

            case 'XSS':                 // Iniezioni XSS
                return array(
                    '<img',
                    'img>',
                    '<image',
                    'document.cookie',
                    'onerror()',
                    'script>',
                    '<script',
                    'alert(',
                    'window.',
                    'String.fromCharCode(',
                    'javascript:',
                    'onmouseover="',
                    '<BODY onload',
                    '<style',
                    'svg onload'
                );

                break;

            default:
                return false;
                break;
        }
    }

    /**
     * Controlla se c'è stato un tentativo di SQL Injection
     * 
     * @param String $Value Stringa da controllare
     * @param String $Method Stringa che indica che array globale ha usato (_POST, _GET, _COOKIE)
     * @param String $DisplayName Stringa che indica la chiave del valore associato
     */
    function sqlCheck($Value, $Method, $DisplayName)
    {
        $BadWords = $this->getArray('SQL');
        foreach ($BadWords as $BadWord) {
            if (strpos(strtolower($Value), strtolower($BadWord)) !== false) {           // Controlla se non contiene vulnerabilità
                $this->vulnDetectedHTML($Method, $BadWord, $DisplayName, 'SQL Injection');    // Richiama la View
            }
        }
    }

    /**
     * Controlla se c'è stato un tentativo di XSS Injection
     * 
     * @param String $Value Stringa da controllare
     * @param String $Method Stringa che indica che array globale ha usato (_POST, _GET, _COOKIE)
     * @param String $DisplayName Stringa che indica la chiave del valore associato
     */
    function xssCheck($Value, $Method, $DisplayName)
    {
        $BadWords = $this->getArray('XSS');

        foreach ($BadWords as $BadWord) {
            if (strpos(strtolower($Value), strtolower($BadWord)) !== false) {
                $this->vulnDetectedHTML($Method, $BadWord, $DisplayName, 'XSS (Cross-Site-Scripting)');
            }
        }
    }

    /**
     * Controlla se è una stringa HTML
     * 
     * @param String $string Stringa da controllare
     */
    function is_html($string)
    {
        return $string != strip_tags($string) ? true : false;
    }



    /* RIPRENDERE DA QUI */




    function santizeString($String)
    {
        $String = escapeshellarg($String);
        $String = htmlentities($String);
        $XSS = $this->getArray('XSS');
        foreach ($XSS as $replace) {
            $String = str_replace($replace, '', $String);
        }
        $SQL = $this->getArray('SQL');
        foreach ($SQL as $replace) {
            $String = str_replace($replace, '', $String);
        }
        return $String;
    }

    function htmlCheck($value, $Method, $DisplayName)
    {
        if ($this->is_html(strtolower($value)) !== false) {
            // HTML Detected!
            $this->vulnDetectedHTML($Method, "HTML CHARS", $DisplayName, 'XSS (HTML)');
        }
    }

    function checkGET()
    {
        foreach ($_GET as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $sub_key => $sub_value) {
                    $this->sqlCheck($sub_value, "_GET", $sub_key);
                    $this->xssCheck($sub_value, "_GET", $sub_key);
                    $this->htmlCheck($sub_value, "_GET", $sub_key);
                }
            } else {
                $this->sqlCheck($value, "_GET", $key);
                $this->xssCheck($value, "_GET", $key);
                $this->htmlCheck($value, "_GET", $key);
            }
        }
    }

    function checkPOST()
    {
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $sub_key => $sub_value) {
                    $this->sqlCheck($sub_value, "_POST", $sub_key);
                    $this->xssCheck($sub_value, "_POST", $sub_key);
                    $this->htmlCheck($sub_value, "_POST", $sub_key);
                }
            } else {
                $this->sqlCheck($value, "_POST", $key);
                $this->xssCheck($value, "_POST", $key);
                $this->htmlCheck($value, "_POST", $key);
            }
        }
    }

    function checkCOOKIE()
    {
        foreach ($_COOKIE as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $sub_key => $sub_value) {
                    $this->sqlCheck($sub_value, "_COOKIE", $sub_key);
                    $this->xssCheck($sub_value, "_COOKIE", $sub_key);
                    $this->htmlCheck($sub_value, "_COOKIE", $sub_key);
                }
            } else {
                $this->sqlCheck($value, "_COOKIE", $key);
                $this->xssCheck($value, "_COOKIE", $key);
                $this->htmlCheck($value, "_COOKIE", $key);
            }
        }
    }

    function gua()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }
        return md5(rand());
    }

    function cutGua($string)
    {
        $five = substr($string, 0, 4);
        $last = substr($string, -3);
        return md5($five . $last);
    }

    function getCSRF()
    {
        if (isset($_SESSION['token'])) {
            $token_age = time() - $_SESSION['token_time'];
            if ($token_age <= 300) {    /* Less than five minutes has passed. */
                return $_SESSION['token'];
            } else {
                $token = md5(uniqid(rand(), TRUE));
                $_SESSION['token'] = $token . "asd648" . $this->cutGua($this->gua());
                $_SESSION['token_time'] = time();
                return $_SESSION['token'];
            }
        } else {
            $token = md5(uniqid(rand(), TRUE));
            $_SESSION['token'] = $token . "asd648" . $this->cutGua($this->gua());
            $_SESSION['token_time'] = time();
            return $_SESSION['token'];
        }
    }

    function verifyCSRF($Value)
    {
        if (isset($_SESSION['token'])) {
            $token_age = time() - $_SESSION['token_time'];
            if ($token_age <= 300) {    /* Less than five minutes has passed. */
                if ($Value == $_SESSION['token']) {
                    $Explode = explode('asd648', $_SESSION['token']);
                    $gua = $Explode[1];
                    if ($this->cutGua($this->gua()) == $gua) {
                        // Validated, Done!
                        unset($_SESSION['token']);
                        unset($_SESSION['token_time']);
                        return true;
                    }
                    unset($_SESSION['token']);
                    unset($_SESSION['token_time']);
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function useCloudflare()
    {
        $this->IPHeader = "HTTP_CF_CONNECTING_IP";
    }

    function useBlazingfast()
    {
        $this->IPHeader = "X-Real-IP";
    }

    function customIPHeader($String = 'REMOTE_ADDR')
    {
        $this->IPHeader = $String;
    }

    function antiCookieSteal($listparams = 'username')
    {
        $this->CookieCheck = true;
        $this->CookieCheckParam = $listparams;
    }

    function cookieCheck()
    {
        // Check Anti-Cookie steal trick.
        if ($this->CookieCheck == true) {
            // Check then.
            if (isset($_SESSION)) { // Session set.
                if (isset($_SESSION[$this->CookieCheckParam])) { // Logged.
                    if (!(isset($_SESSION['xWAF-IP']))) {
                        $_SESSION['xWAF-IP'] = $_SERVER[$this->IPHeader];
                        return true;
                    } else {
                        if (!($_SESSION['xWAF-IP'] == $_SERVER[$this->IPHeader])) {
                            // Changed IP.
                            unset($_SESSION['xWAF-IP']);
                            unset($_SESSION);
                            @session_destroy();
                            @session_start();
                            return true;
                        }
                    }
                }
            }
        }
    }
    function start()
    {
        // @session_start();
        @$this->checkGET();
        @$this->checkPOST();
        @$this->checkCOOKIE();
        if ($this->CookieCheck == true) {
            $this->cookieCheck();
        }
    }
}
