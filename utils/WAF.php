<?php

namespace utils;

class WAF
{

    /**
     * Costruttore dell'oggetto WAF  (Web Application Firewall)
     */
    function __construct()
    {
        //controllo che l'ip non sia stato bloccato
        $ip = empty($_SERVER['REMOTE_ADDR']) ? null : $_SERVER['REMOTE_ADDR']; //prendo l'ip del client
        if (($blockedIP = $this->isAlreadyBlocked($ip)) !== FALSE) {
            $inj_ID = $blockedIP->getInjId();
            $TypeVuln = $blockedIP->getTypeVuln();
            $time = strtotime(explode(" ", $blockedIP->getTimestamp())[1]);
            $currentTimeBlock = time() - $time;
            if ($currentTimeBlock >= 3600) {
                \models\DAOBlockedIp::removeBlockedIp($ip);
            } else {
                die(include('./views/denyPage.php'));
            }
        }
        $this->startSession();
        $this->filter();
    }

    /**
     * Inizia le operazioni di controllo sull'array GET, POST e COOKIE
     */
    function filter()
    {
        $this->checkGET();
        $this->checkPOST();
        $this->checkCOOKIE();
    }

    /**
     * Mostra la View di blocco
     * 
     * @param String $inj_ID ID che identifica che BadWord è stata usata
     * @param String $TypeVuln Stringa che identifica il tipo di vulnerabilità identificata
     */
    private function showBlockPage($inj_ID, $TypeVuln)
    {
        header('HTTP/1.1 403 Forbidden');
        //TODO blocco dell'indirizzo ip e, se presente, del profilo per un'ora
        $ip = empty($_SERVER['REMOTE_ADDR']) ? null : $_SERVER['REMOTE_ADDR']; //prendo l'ip del client
        try {
            $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
            $inj_ID = md5($inj_ID);     // Creazione Block ID
            $blockedIp = new \models\DOBlockedIp($ip, $userId, $inj_ID, $TypeVuln);
            \models\DAOBlockedIp::insertBlockedIp($blockedIp);
            $currentTimeBlock = 3600;
        } catch (\Exception $e) {
            die(include('./views/denyPage.php'));
        }
        die(include('./views/denyPage.php'));          // Blocca la richiesta e mostra la View
    }

    /**
     * Funzione che controlla se un determinato ip è gia bloccato
     * 
     * @param String $ip Indirizzo IP da controllare
     * 
     * @return TRUE:FALSE True se trova l'IP altrimenti False
     */
    private function isAlreadyBlocked($ip)
    {
        try {
            $blockedIP = \models\DAOBlockedIp::getBlockedIp(array('ip' => $ip));
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        if ($blockedIP !== null) {
            return $blockedIP;
        }
        return false;
    }

    /**
     * Funzione per filtrare una stringa da caratteri speciali (per la messaggistica)
     * @todo
     */
    function clearInput($string)
    {
        return preg_replace("[^\w\.@-]", "", $string);      // Pag.520
    }

    /**
     * Inizializza la sessione oppure ricrea un nuovo ID di sessione per quelle vecchie
     * 
     * @todo
     */
    function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {     //se la sessione non è avviata la avvio
            session_start();
        } else {
            if (!empty($_SESSION['sessionTTL']) && $_SESSION['sessionTTL'] < time() - 600) {        // se la sessione 
                var_dump(session_id());
                echo "<br>";
                var_dump(session_regenerate_id());
                echo "<br><br>Parmas Session<br>";
                var_dump($_SESSION);
            }
        }
    }

    /**
     * Ottiene un array di valori usati per le iniezioni di SQL o XSS
     * 
     * @param String $Type Il tipo di iniezione da cui ricavare i valori
     * 
     * @return Array Gli array di valori malevoli
     */
    private function getArray($type)
    {
        // Alcuni possibili valori di iniezione
        switch ($type) {
            case 'SQL':                 // Iniezioni SQL
                return array(
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
        }
    }

    /**
     * Controlla se c'è stato un tentativo di SQL Injection
     * 
     * @param String $Value Stringa da controllare
     */
    function sqlCheck($Value)
    {
        $BadWords = $this->getArray('SQL');
        foreach ($BadWords as $BadWord) {
            if (strpos(strtolower($Value), strtolower($BadWord)) !== false) {           // Controlla se non contiene vulnerabilità
                $inj_ID = array_keys($BadWords, $BadWord)[0];
                $this->showBlockPage($inj_ID, 'SQL Injection');    // Richiama la View
            }
        }
    }

    /**
     * Controlla se c'è stato un tentativo di XSS Injection
     * 
     * @param String $Value Stringa da controllare
     */
    function xssCheck($Value)
    {
        $BadWords = $this->getArray('XSS');

        foreach ($BadWords as $BadWord) {
            if (strpos(strtolower($Value), strtolower($BadWord)) !== false) {
                $inj_ID = array_keys($BadWords, $BadWord)[0];
                $this->showBlockPage($inj_ID, 'XSS (Cross-Site-Scripting)');     // I primi tre parametri funzono solo per creare un Block ID
            }
        }
    }

    /**
     * Controlla se c'è stato un tentativo di HTML injection
     */
    function htmlCheck($Value)
    {
        if (strpos(htmlentities($Value), '&') !== FALSE || strpos(htmlentities($Value), '&') === 0) {
            $inj_ID = "99";
            $this->showBlockPage($inj_ID, 'XSS (HTML)');
        }
    }

    /**
     * Per ogni elemento passato tramite GET, viene verificato che non sia un tentativo di SQL, XSS e HTML injections
     */
    function checkGET()
    {
        foreach ($_GET as $value) {
            if (is_array($value)) {
                foreach ($value as $sub_value) {
                    $this->sqlCheck($sub_value);
                    $this->xssCheck($sub_value);
                    $this->htmlCheck($sub_value);
                }
            } else {
                $this->sqlCheck($value);
                $this->xssCheck($value);
                $this->htmlCheck($value);
            }
        }
    }

    /**
     * Per ogni elemento passato tramite POST, viene verificato che non sia un tentativo di SQL, XSS e HTML injections
     */
    function checkPOST()
    {
        foreach ($_POST as $value) {
            if (is_array($value)) {
                foreach ($value as $sub_value) {
                    $this->sqlCheck($sub_value);
                    $this->xssCheck($sub_value);
                    $this->htmlCheck($sub_value);
                }
            } else {
                $this->sqlCheck($value);
                $this->xssCheck($value);
                $this->htmlCheck($value);
            }
        }
    }

    /**
     * Per ogni elemento passato tramite COOKIE, viene verificato che non sia un tentativo di SQL, XSS e HTML injections
     */
    function checkCOOKIE()
    {
        foreach ($_COOKIE as $value) {
            if (is_array($value)) {
                foreach ($value as $sub_value) {
                    $this->sqlCheck($sub_value);
                    $this->xssCheck($sub_value);
                    $this->htmlCheck($sub_value);
                }
            } else {
                $this->sqlCheck($value);
                $this->xssCheck($value);
                $this->htmlCheck($value);
            }
        }
    }


    /**
     * Recupera il token CSRF oppure ne crea uno nuovo se non esiste o se scaduto
     * 
     * @return String Un token generato casualmente e salvato in sessione
     */
    static function getCSRF()
    {
        if (isset($_SESSION['token'])) {
            $tokenTTL = time() - $_SESSION['token_time'];       // Ottiene il tempo di vita del token

            if ($tokenTTL <= 300) {    // Se il tempo di vita del token è meno di 5 minuti allora è ancora attivo
                return $_SESSION['token'];
            } else {        // altrimenti ne viene generato un altro
                $_SESSION['token'] = md5(bin2hex(random_bytes(32)));
                $_SESSION['token_time'] = time();
            }
        } else {        // Altrimenti ne genera uno nuovo
            $_SESSION['token'] = md5(bin2hex(random_bytes(32)));
            $_SESSION['token_time'] = time();
        }

        return $_SESSION['token'];
    }

    /**
     * Verifica che il token passato dal form di login sia quello salvato in sessione,
     * questo per evitare operazioni indesiderate di Cross-Site Request Forgery (CSRF)
     * 
     * @param String $formToken Il token passato dal form della log-in
     * 
     * @return True Nel caso sia stato validato il token
     * 
     * @throws Exception Nel caso il token non sia stato validato, fosse scaduto oppure non fosse esistente in sessione
     */
    static function verifyCSRF($formToken)
    {
        if (isset($_SESSION['token'])) {
            $tokenTTL = time() - $_SESSION['token_time'];

            if ($tokenTTL <= 300) {      // Controlla il Time To Live del token (5 Minuti)
                if ($formToken === $_SESSION['token']) {
                    unset($_SESSION['token']);
                    unset($_SESSION['token_time']);
                    return true;
                } else {
                    unset($_SESSION['token']);
                    unset($_SESSION['token_time']);

                    throw new \Exception('Token non valido');
                }
            } else {
                throw new \Exception('Token scaduto');
            }
        }
        throw new \Exception('Token non impostato');
    }
}
