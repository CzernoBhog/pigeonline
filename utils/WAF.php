<?php

namespace utils;

class WAF
{
    /**
     * Costruttore dell'oggetto WAF  (Web Application Firewall): Avvia il WAF inizializzando la sessione,
     * controllando se l'IP è bloccato o meno e controllando i dati ricevuti
     */
    function __construct()
    {
        $this->startSession();
        $this->isBlockedIP();

        if(isset($_POST['messageText']) && $_REQUEST['action'] === 'sendMessage') {
            $this->textMsgFilter($_POST['messageText']);
        } else {
            $this->filter();
        }
    }

    /**
     * Inizia le operazioni di controllo sull'array GET, POST e COOKIE di PHP
     */
    function filter()
    {
        $this->checkGET();
        $this->checkPOST();
        $this->checkCOOKIE();
    }

    /**
     * Trasforma i caratteri del testo in entità HTML (Usato per i messaggi)
     * 
     * @param String $text Stringa da convertire
     */
    function textMsgFilter(&$text) {
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Inizializza la sessione oppure ricrea un nuovo ID di sessione per quelle più vecchie di 10 minuti
     */
    function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {     //se la sessione non è avviata la avvio
            session_start();
        }

        // se la sessione è vecchia, rigenera il suo ID     (dopo 10 minuti)
        if (!empty($_SESSION['sessionTTL']) && $_SESSION['sessionTTL'] < strtotime("-10 minutes", time())) {
            session_regenerate_id();
            $_SESSION['sessionTTL'] = time();
        }

    }

    /**
     * Genera una chiave pubblica e una privata per l'utente
     * 
     * @todo
     */
    /*static function generateEncryptionKeys() {
        // Genera una chiave privatae e una chiave pubblica, restituendo un ID della risorsa
        $resource = openssl_pkey_new(array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        // Estrae la chiave privata dalla risorsa, sottoforma di stringa
        openssl_pkey_export($resource, $privatekey);

        // Estrae la chiave pubblica dalla risorsa, sottoforma di stringa   (la funzione restituisce un array di dettagli)
        $publickey = openssl_pkey_get_details($resource)["key"];

        // TODO: Trovare un posto per la chiave pubblica e uno sicuro per la chiave privata
        
    }*/

    /**
     * Mostra la View di blocco
     * 
     * @param String $inj_ID ID che identifica che BadWord è stata usata
     * @param String $TypeVuln Stringa che identifica il tipo di vulnerabilità identificata
     */
    function showBlockPage($inj_ID, $TypeVuln)
    {
        header('HTTP/1.1 403 Forbidden');
        //TODO blocco dell'indirizzo ip e, se presente, del profilo per un'ora
        $ip = empty($_SERVER['REMOTE_ADDR']) ? null : $_SERVER['REMOTE_ADDR']; //prendo l'ip del client
        try {
            $userId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
            
            session_unset();        // unsetta le variabili di sessione per inserire quelle di blocco per la redirection
            $_SESSION['ip'] = $ip;
            $_SESSION['inj_ID'] = md5($inj_ID);     // Creazione Block ID
            $_SESSION['typeVuln'] = $TypeVuln;
            $_SESSION['currentTimeBlock'] = 3600;

            $blockedIp = new \models\DOBlockedIp($ip, $userId, $inj_ID, $TypeVuln);
            \models\DAOBlockedIp::insertBlockedIp($blockedIp);
        } catch (\Exception $e) {
            if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') || isset($_POST['ajax'])) {
                echo "blocked";
                die();
            } else {
                die(include('./views/denyPage.php'));
            }
        }

        if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') || isset($_POST['ajax'])) {
            echo "blocked";
            die();
        } else {
            die(include('./views/denyPage.php'));
        }
    }

    /**
     * Controlla se l'IP è bloccato e che l'utente registrato sia bloccato
     */
    private function isBlockedIP()
    {
        try {
            $ip = empty($_SERVER['REMOTE_ADDR']) ? null : $_SERVER['REMOTE_ADDR'];
            // l'operatore "??" assegna la prima espressione se esiste ed è diversa da NULL e fa la stessa cosa per la seconda, nel caso la prima fosse NULL
            $blocked = \models\DAOBlockedIp::getBlockedIp(array('ip' => $ip)) ?? (isset($_SESSION['id']) ? \models\DAOBlockedIp::getBlockedIp(array('userId' => $_SESSION['id'])) : null);

            if ($blocked !== NULL) {
                $inj_ID = $blocked->getInjId();
                $TypeVuln = $blocked->getTypeVuln();

                // Converte le stringhe ottenute in Timestamps
                $blockDate = strtotime($blocked->getTimestamp());
                $currentDate = strtotime(date('Y-m-d H:i:s'));

                // Calcola la differenza           
                $currentTimeBlock = $currentDate - $blockDate;
                if ($currentTimeBlock >= 3600) {
                    \models\DAOBlockedIp::removeBlockedIp($ip);
                } else {
                    if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') || isset($_POST['ajax'])) {
                        echo "blocked";
                        die();
                    } else {
                        die(include('./views/denyPage.php'));
                    }
                }
            }
        } catch (\Exception $e) {
            $message = "Qualcosa è andato storto, riprova più tardi";
            die(include('./view/messagePage.php'));
        }
    }

    /**
     * Ottiene un array di valori usati per le iniezioni di SQL o XSS;
     * gli indici alle relative BadWords servono per identificarle tramite il Block ID
     * 
     * (99 è per tentato XSS [HTML] injection e 98 per tentato accesso alle chat altrui)
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
                    1 => '´',
                    2 => 'SELECT FROM',
                    3 => 'SELECT * FROM',
                    4 => 'SET ',
                    5 => 'DROP',
                    6 => 'UNION',
                    7 => 'UPDATE ',
                    8 => 'WHERE ',
                    9 => 'DATABASE',
                    10 => '0x50',
                    11 => 'mid((select',
                    12 => 'union(((((((',
                    13 => 'concat(0x',
                    14 => 'concat(',
                    15 => 'OR boolean',
                    16 => 'or HAVING',
                    17 => "OR '1",
                    18 => '0x3c62723e3c62723e3c62723e',
                    19 => '0x3c696d67207372633d22',
                    20 => '+#1q%0AuNiOn all#qa%0A#%0AsEleCt',
                    21 => 'unhex(hex(Concat(',
                    22 => 'Table_schema,0x3e,',
                    23 => '0x00', // \0
                    24 => '0x08', // \b
                    25 => '0x09', // \t
                    26 => '0x0a', // \n
                    27 => '0x0d', // \r
                    28 => '0x1a', // \Z
                    29 => '0x22', // \"
                    30 => '0x25', // \%
                    31 => '0x27', // \'
                    32 => '0x5c', // \\
                    33 => '0x5f'  // \_
                );

                break;

            case 'XSS':                 // Iniezioni XSS
                return array(
                    34 => '<img',
                    35 => 'img>',
                    36 => '<image',
                    37 => 'document.cookie',
                    38 => 'onerror()',
                    39 => 'script>',
                    40 => '<script',
                    41 => 'alert(',
                    42 => 'window.',
                    43 => 'String.fromCharCode(',
                    44 => 'javascript:',
                    45 => 'onmouseover="',
                    46 => '<BODY onload',
                    47 => '<style',
                    48 => 'svg onload',
                    49 => 'src=',
                    50 => 'href='
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
     * Recupera il token CSRF oppure ne crea uno nuovo se non esiste o se è scaduto
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

    /*
        <post action="www.banca.com/index.php?action=aggiornaProfilo">
            <img>
            <input> ---> password
        </post>
        
    */

    /**
     * Verifica che il token passato dal form di login sia quello salvato in sessione,
     * questo per evitare operazioni indesiderate di Cross-Site Request Forgery (CSRF)
     * 
     * @param String $formToken Il token passato dal form della log-in
     * 
     * @return True Nel caso sia stato validato il token
     * 
     * @throws Exception Nel caso il token non sia stato validato, sia scaduto oppure non esista in sessione
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
