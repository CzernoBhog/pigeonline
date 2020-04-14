<!DOCTYPE html>
<html lang="en" xmlns="//www.w3.org/1999/xhtml">

<head>
    <section class="center clearfix">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>xWAF - Access Denied</title>
        <link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet" type="text/css">
        <link href="./utils/css/denyPage.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div id="main-container">
        <header class="app-header clearfix">
            <div class="wrap"><span class="logo-neartext">Web Application Firewall</span><a target="_blnak" href="https://github.com/Alemalakra/xWAF/" class="site-link">Github</a></div>
        </header>
        <section class="app-content access-denied clearfix">
            <div class="box center width-max-940">
                <h1 class="brand-font font-size-xtra no-margin"><i class="icon-circle-red"></i>Access Denied - xWAF</h1>
                <p class="medium-text code-snippet">If you think this block is an error please <a href="mailto:pigeonline.project@gmail.com">contact firewall developer</a> and make sure to include the block
                    details (displayed in the box below), so we can assist you in troubleshooting the issue. </p>
                <h2>Block details:</h1>
                    <table class="property-table overflow-break-all line-height-16">
                        <tr>
                            <td>Your IP:</td>
                            <td><span><?= $_SERVER[$this->IPHeader] ?></span></td>
                        </tr>
                        <tr>
                            <td>URL:</td>
                            <td>
                                <span>
                                    <?= htmlspecialchars( $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8' ) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Your Browser: </td>
                            <td><span><?= htmlspecialchars($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <tr>
                            <td>Block ID:</td>
                            <td>
                                <span>
                                    <!-- CREAZIONE DEL BLOCK ID  -->
                                    <?= //$this->shorten_string( md5( $TypeVuln . $Method . $DisplayName . $_SERVER[$this->IPHeader] . date('DmY')), 7 ) 
                                        md5( $TypeVuln . $Method . $DisplayName . $_SERVER[$this->IPHeader] . date('DmY')) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Block reason:</td>
                            <td><span>An attempted <?= htmlentities($TypeVuln) ?> was detected and blocked.</span></td>
                        </tr>
                        <tr>
                            <td>Time:</td>
                            <td><span><?= date('Y-m-d H:i:s') ?></span></td>
                        </tr>
                    </table>
            </div>
        </section>
        <footer><span>&copy; 2018 xWAF - Free Open-Source Web Application Firewall.</span><span id="privacy-policy">
                <a href="https://github.com/Alemalakra/xWAF/" target="_blank" rel="nofollow noopener">Github</a></span>
        </footer>
    </div>
</body>

</html>