===== ENVIRONMENT ORION =====
/etc/orion/orion.env

===== ORION ENV REFERENCES =====
/etc/orion/orion.env:3:APP_URL=https://orion.nebuladet.website
/etc/orion/orion.env:4:ORION_AUTH_DB_DSN=pgsql:host=aws-0-us-east-2.pooler.supabase.com;port=5432;dbname=postgres;sslmode=require
/etc/orion/orion.env:5:ORION_AUTH_DB_USERNAME=postgres.redacted
/etc/orion/orion.env:6:ORION_AUTH_DB_PASSWORD=redacted
[ec2-user@ip-172-31-1-105 ~]$



 echo '===== ORION DB REFERENCES ====='

sudo grep -RniE \
    'CREATE TABLE|INSERT INTO|users|usuarios|user_root|root_user|auth_users|password_hash' \
    /var/www/orion.nebuladet.website \
    /home/ec2-user/orion \
    2>/dev/null | head -300
===== ORION DB REFERENCES =====
/var/www/orion.nebuladet.website/public/auth_root.php:20:            'user_root'=>['id'=>$user['id']??null,'usuario'=>$user['usuario']??null,'nombre'=>$user['nombres']??null,'email'=>$user['email']??null],
/var/www/orion.nebuladet.website/public/credito_fiscal.php:73:            $userStmt = $pdo->prepare('SELECT id::text FROM auth.users WHERE email = ? LIMIT 1');
/var/www/orion.nebuladet.website/public/credito_fiscal.php:74:            $userStmt->execute([$sessionEmail]);
/var/www/orion.nebuladet.website/public/credito_fiscal.php:75:            $supabaseUserId = $userStmt->fetchColumn() ?: null;
/var/www/orion.nebuladet.website/public/credito_fiscal.php:77:            error_log('No se pudo resolver auth.users para catalogos CCF: ' . $userLookupError->getMessage());
/var/www/orion.nebuladet.website/public/factura_pro.php:492:            $users = SupabaseConnector::consultar("dte_usuarios?id=eq.$userId");
/var/www/orion.nebuladet.website/public/factura_pro.php:493:            $userData = $users[0] ?? null;
/var/www/orion.nebuladet.website/public/session_manager.php:211:        return self::has('usuario_id') || self::has('user_root');
/var/www/orion.nebuladet.website/public/session_manager.php:218:        if (self::has('user_root')) {
/var/www/orion.nebuladet.website/public/session_manager.php:219:            return self::get('user_root');
/var/www/orion.nebuladet.website/public/app/auth.php:30:        return (array)SessionManager::get('user_root', []);
/home/ec2-user/orion/backup-20260919-173352/kernell/composer.lock:1060:                    "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/bacon/bacon-qr-code/src/Renderer/Image/SvgImageBackEnd.php:281:        $this->xmlWriter->writeAttribute('gradientUnits', 'userSpaceOnUse');
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/LICENSE:18:free software--to make sure the software is free for all its users.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/LICENSE:61:effectively restrict the users of a free program by obtaining a
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/LICENSE:105:users' freedom, it does ensure that the user of a program that is
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/README.md:87:While installing the entire package manually or with Composer is simple, convenient, and reliable, you may want to include only vital files in your project. At the very least you will need [src/PHPMailer.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/PHPMailer.php). If you're using SMTP, you'll need [src/SMTP.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/SMTP.php), and if you're using POP-before SMTP (*very* unlikely!), you'll need [src/POP3.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/POP3.php). You can skip the [language](https://github.com/PHPMailer/PHPMailer/tree/master/language/) folder if you're not showing errors to users and can make do with English-only errors. If you're using XOAUTH2 you will need [src/OAuth.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/OAuth.php) as well as the Composer dependencies for the services you wish to authenticate with. Really, it's much easier to use Composer!
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/composer.json:16:            "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:10: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:179:    //Use this to interact with an API on the users behalf
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/DSNConfigurator.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/Exception.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:29: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/POP3.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/POP3.php:40: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:1118:     * will send the message to the users terminal if they are logged
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:709:The diff might look very big but 95% of Guzzle users will be able to upgrade without modification.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1157:  caused problems for many users: they aren't PSR-4 compliant, require an
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1378:* Adding more information to ExceptionCollection exceptions so that users have more context, including a stack trace of
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1489:  Symfony users can still use the old version of Monolog.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/guzzlehttp/guzzle/UPGRADING.md:1049:    "description":"Provides access to Zendesk views, groups, tickets, ticket fields, and users",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/guzzlehttp/guzzle/src/Middleware.php:200:        // To be compatible with Guzzle 7.1.x we need to allow users to pass a MessageFormatter
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:271:        $insertSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (?, ?, ?, ?)";
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:121:            'mysql' => "CREATE TABLE $this->table ($this->idCol VARBINARY(255) NOT NULL PRIMARY KEY, $this->dataCol MEDIUMBLOB NOT NULL, $this->lifetimeCol INTEGER UNSIGNED, $this->timeCol INTEGER UNSIGNED NOT NULL) ENGINE = InnoDB",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:122:            'sqlite' => "CREATE TABLE $this->table ($this->idCol TEXT NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:123:            'pgsql' => "CREATE TABLE $this->table ($this->idCol VARCHAR(255) NOT NULL PRIMARY KEY, $this->dataCol BYTEA NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:124:            'oci' => "CREATE TABLE $this->table ($this->idCol VARCHAR2(255) NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:125:            'sqlsrv' => "CREATE TABLE $this->table ($this->idCol VARCHAR(255) NOT NULL PRIMARY KEY, $this->dataCol VARBINARY(MAX) NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:261:        $insertSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :lifetime, :time)";
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/polyfill-ctype/README.md:4:This component provides `ctype_*` functions to users who run php versions without the ctype extension.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/validator/ConstraintValidator.php:67:     * should only be displayed for technical users. Non-technical users
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:330:            'mysql' => "CREATE TABLE $this->table ($this->idCol VARBINARY(128) NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER UNSIGNED NOT NULL, $this->timeCol INTEGER UNSIGNED NOT NULL) ENGINE = InnoDB",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:331:            'sqlite' => "CREATE TABLE $this->table ($this->idCol TEXT NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:332:            'pgsql' => "CREATE TABLE $this->table ($this->idCol VARCHAR(128) NOT NULL PRIMARY KEY, $this->dataCol BYTEA NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:333:            'oci' => "CREATE TABLE $this->table ($this->idCol VARCHAR2(128) NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:334:            'sqlsrv' => "CREATE TABLE $this->table ($this->idCol VARCHAR(128) NOT NULL PRIMARY KEY, $this->dataCol VARBINARY(MAX) NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:871:                $sql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, EMPTY_BLOB(), :expiry, :time) RETURNING $this->dataCol into :data";
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:877:                $sql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :expiry, :time)";
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:881:                $sql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :expiry, :time)";
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:934:                $mergeSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :expiry, :time) ".
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:948:                $mergeSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :expiry, :time) ".
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/EventStreamResponse.php:18: * To broadcast events to multiple users at once, for long-running
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/symfony/http-foundation/Request.php:817:        // the check for $this->session avoids malicious users trying to fake a session cookie with proper name
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/ramsey/collection/src/DoubleEndedQueueInterface.php:157: * do so. Users of any `DoubleEndedQueueInterface` implementations that do allow
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/psr/http-message/src/ServerRequestInterface.php:36: * content, matching authorization headers to users, etc). These parameters
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/psr/http-message/src/UriInterface.php:258:     * Users can provide both encoded and decoded path characters.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/psr/http-message/src/UriInterface.php:273:     * Users can provide both encoded and decoded query characters.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/psr/http-message/src/UriInterface.php:290:     * Users can provide both encoded and decoded fragment characters.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/composer/installed.json:1096:                    "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/CHANGELOG.md:165:array->object/enum changes, but there is no big new feature for end users.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/CHANGELOG.md:409:  * Added ElasticsearchHandler to send records via the official ES library. Elastica users should now use ElasticaHandler instead of ElasticSearchHandler
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/CHANGELOG.md:476:  * Added InsightOpsHandler to migrate users of the LogEntriesHandler
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/CHANGELOG.md:647:  * Added $host to HipChatHandler for users of private instances
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/CHANGELOG.md:741:  * Added support for sending messages to multiple users at once with the PushoverHandler
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/PHPConsoleHandler.php:38: *      $logger->debug('SELECT * FROM users', array('db', 'time' => 0.012));
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:31:    private array $users;
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:81:     * @param string|array $users  Pushover user id or array of ids the message will be sent to
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:83:     * @param bool         $useSSL Whether to connect via SSL. Required when pushing messages to users that are not
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:96:     * @phpstan-param string|array<int|string>    $users
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:102:        $users,
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:130:        $this->users = (array) $users;
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:199:        foreach ($this->users as $user) {
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/monolog/monolog/src/Monolog/Handler/TelegramBotHandler.php:80:     * Sends the message silently. Users will receive a notification with no sound.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:4268:      * @param mixed $subset if true embed only a subset of the font (stores only the information related to the used characters); if false embed full font; if 'default' uses the default value set using setFontSubsetting(). This option is valid only for TrueTypeUnicode fonts. If you want to enable users to change the document, set this parameter to false. If you subset the font, the person who receives your PDF would need to have your same font in order to make changes to your PDF. The file size of the PDF would also be smaller because you are embedding only part of a font.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:4538:      * @param mixed $subset if true embed only a subset of the font (stores only the information related to the used characters); if false embed full font; if 'default' uses the default value set using setFontSubsetting(). This option is valid only for TrueTypeUnicode fonts. If you want to enable users to change the document, set this parameter to false. If you subset the font, the person who receives your PDF would need to have your same font in order to make changes to your PDF. The file size of the PDF would also be smaller because you are embedding only part of a font.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:11035:     * @param array $permissions the set of permissions (specify the ones you want to block):<ul><li>print : Print the document;</li><li>modify : Modify the contents of the document by operations other than those controlled by 'fill-forms', 'extract' and 'assemble';</li><li>copy : Copy or otherwise extract text and graphics from the document;</li><li>annot-forms : Add or modify text annotations, fill in interactive form fields, and, if 'modify' is also set, create or modify interactive form fields (including signature fields);</li><li>fill-forms : Fill in existing interactive form fields (including signature fields), even if 'annot-forms' is not specified;</li><li>extract : Extract text and graphics (in support of accessibility to users with disabilities or for other purposes);</li><li>assemble : Assemble the document (insert, rotate, or delete pages and create bookmarks or thumbnail images), even if 'modify' is not set;</li><li>print-high : Print the document to a representation from which a faithful digital copy of the PDF content could be generated. When this is not set, printing is limited to a low-level representation of the appearance, possibly of degraded quality.</li><li>owner : (inverted logic - only for public-key) when set permits change of encryption and enables all other permissions.</li></ul>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/CHANGELOG.TXT:224:  - Fixed erase users pictures
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:200:software for all its users.  We, the Free Software Foundation, use the
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:228:that there is no warranty for this free software.  For both users' and
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:233:  Some devices are designed to deny users access to install or run
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:236:protecting users' freedom to change the software.  The systematic
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:242:of the GPL, as needed to protect the freedom of users.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:330:  The Corresponding Source need not include anything that users
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:362:  3. Protecting Users' Legal Rights From Anti-Circumvention Law.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:375:users, your or third parties' legal rights to forbid circumvention of
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:423:used to limit the access or legal rights of the compilation's users
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/README.md:20:All users are invited to migrate to [tc-lib-pdf](https://github.com/tecnickcom/tc-lib-pdf), the modern and modular successor.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/AUTHORS:111:* Maxim Iorsh <iorsh AT users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:17:software for all its users.  We, the Free Software Foundation, use the
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:45:that there is no warranty for this free software.  For both users' and
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:50:  Some devices are designed to deny users access to install or run
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:53:protecting users' freedom to change the software.  The systematic
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:59:of the GPL, as needed to protect the freedom of users.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:147:  The Corresponding Source need not include anything that users
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:179:  3. Protecting Users' Legal Rights From Anti-Circumvention Law.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:192:users, your or third parties' legal rights to forbid circumvention of
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:240:used to limit the access or legal rights of the compilation's users
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/CREDITS:35:it a lot easier for TeX users to cope with multiple or complex languages,
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/CREDITS:201:users, enthusiasts and software developers for their work in Indian
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/CREDITS:290:* Maxim Iorsh <iorsh AT users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/ChangeLog:440:      to Mac OS 10.6 users, who suffer from a bug.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/ChangeLog:1494:     /pub/users/ucgadkw/indology/software/sinhala1-TeX.zip
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/INSTALL:16:Users of Debian GNU/Linux system will probably want to use the Debian package,
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/INSTALL:29:Users of KDE can install .ttf files on a per-user basis using the KDE
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/INSTALL:71: In order to use OpenType, users of Windows 95, 98 and NT 4.0 can
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/INSTALL:82:depending on whether they should be available to all users on your system
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/AUTHORS:111:* Maxim Iorsh <iorsh AT users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:17:software for all its users.  We, the Free Software Foundation, use the
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:45:that there is no warranty for this free software.  For both users' and
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:50:  Some devices are designed to deny users access to install or run
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:53:protecting users' freedom to change the software.  The systematic
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:59:of the GPL, as needed to protect the freedom of users.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:147:  The Corresponding Source need not include anything that users
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:179:  3. Protecting Users' Legal Rights From Anti-Circumvention Law.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:192:users, your or third parties' legal rights to forbid circumvention of
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:240:used to limit the access or legal rights of the compilation's users
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/CREDITS:35:it a lot easier for TeX users to cope with multiple or complex languages,
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/CREDITS:200:users, enthusiasts and software developers for their work in Indian
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/CREDITS:288:* Maxim Iorsh <iorsh AT users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/ChangeLog:5157:       10.6 users, who suffer from a bug.
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/ChangeLog:6670:       /pub/users/ucgadkw/indology/software/sinhala1-TeX.zip The hope is
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/INSTALL:21:Users of Debian GNU/Linux system will probably want to use the Debian package,
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/INSTALL:30:Users of KDE can install .ttf files on a per-user basis using the KDE
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/INSTALL:80: In order to use OpenType, users of Windows 95, 98 and NT 4.0 can
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/INSTALL:94:depending on whether they should be available to all users on your system
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/ae_fonts_2.0/COPYING:16:software--to make sure the software is free for all its users.  This
/home/ec2-user/orion/backup-20260919-173352/kernell/vendor/tecnickcom/tcpdf/fonts/ae_fonts_2.0/COPYING:110:    a warranty) and that users may redistribute the program under
/home/ec2-user/orion/backup-20260919-173352/kernell/app/Core/Database/QueryBuilder.php:94:        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");
/home/ec2-user/orion/backup-20260919-173352/kernell/app/Services/Auth/LoginServide.php:30:            'SELECT * FROM usuarios WHERE email = :email AND activo = 1 LIMIT 1'
/home/ec2-user/orion/backup-20260919-173352/kernell/app/Services/Auth/LoginServide.php:39:        if (!password_verify($password, (string) ($user['password_hash'] ?? ''))) {
/home/ec2-user/orion/backup-20260919-173352/kernell/app/Services/Auth/RefreshTokenService.php:22:            'INSERT INTO user_sessions
/home/ec2-user/orion/backup-20260919-173352/kernell/app/Services/Auth/RefreshTokenService.php:45:             INNER JOIN usuarios u ON u.usuario_id = us.usuario_id
/home/ec2-user/orion/backup-20260919-173352/kernell/app/Services/DTE/DteTenantConfigurationWriter.php:66:        $sql = 'INSERT INTO tenant_dte_configurations
/home/ec2-user/orion/backup-20260919-173352/kernell/app/Services/Queue/QueueDispatcher.php:32:            'INSERT INTO background_jobs
/home/ec2-user/orion/backup-20260919-174130/kernell/app/Services/Auth/LoginServide.php:30:            'SELECT * FROM usuarios WHERE email = :email AND activo = 1 LIMIT 1'
/home/ec2-user/orion/backup-20260919-174130/kernell/app/Services/Auth/LoginServide.php:39:        if (!password_verify($password, (string) ($user['password_hash'] ?? ''))) {
/home/ec2-user/orion/backup-20260919-174130/kernell/app/Services/Auth/RefreshTokenService.php:22:            'INSERT INTO user_sessions
/home/ec2-user/orion/backup-20260919-174130/kernell/app/Services/Auth/RefreshTokenService.php:45:             INNER JOIN usuarios u ON u.usuario_id = us.usuario_id
/home/ec2-user/orion/backup-20260919-174130/kernell/app/Services/DTE/DteTenantConfigurationWriter.php:66:        $sql = 'INSERT INTO tenant_dte_configurations
/home/ec2-user/orion/backup-20260919-174130/kernell/app/Services/Queue/QueueDispatcher.php:32:            'INSERT INTO background_jobs
/home/ec2-user/orion/backup-20260919-174130/kernell/app/Core/Database/QueryBuilder.php:94:        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");
/home/ec2-user/orion/backup-20260919-174130/kernell/composer.lock:1060:                    "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/CHANGELOG.md:165:array->object/enum changes, but there is no big new feature for end users.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/CHANGELOG.md:409:  * Added ElasticsearchHandler to send records via the official ES library. Elastica users should now use ElasticaHandler instead of ElasticSearchHandler
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/CHANGELOG.md:476:  * Added InsightOpsHandler to migrate users of the LogEntriesHandler
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/CHANGELOG.md:647:  * Added $host to HipChatHandler for users of private instances
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/CHANGELOG.md:741:  * Added support for sending messages to multiple users at once with the PushoverHandler
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/PHPConsoleHandler.php:38: *      $logger->debug('SELECT * FROM users', array('db', 'time' => 0.012));
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:31:    private array $users;
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:81:     * @param string|array $users  Pushover user id or array of ids the message will be sent to
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:83:     * @param bool         $useSSL Whether to connect via SSL. Required when pushing messages to users that are not
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:96:     * @phpstan-param string|array<int|string>    $users
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:102:        $users,
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:130:        $this->users = (array) $users;
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:199:        foreach ($this->users as $user) {
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/monolog/monolog/src/Monolog/Handler/TelegramBotHandler.php:80:     * Sends the message silently. Users will receive a notification with no sound.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/INSTALL:16:Users of Debian GNU/Linux system will probably want to use the Debian package,
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/INSTALL:29:Users of KDE can install .ttf files on a per-user basis using the KDE
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/INSTALL:71: In order to use OpenType, users of Windows 95, 98 and NT 4.0 can
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/INSTALL:82:depending on whether they should be available to all users on your system
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/AUTHORS:111:* Maxim Iorsh <iorsh AT users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:17:software for all its users.  We, the Free Software Foundation, use the
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:45:that there is no warranty for this free software.  For both users' and
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:50:  Some devices are designed to deny users access to install or run
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:53:protecting users' freedom to change the software.  The systematic
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:59:of the GPL, as needed to protect the freedom of users.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:147:  The Corresponding Source need not include anything that users
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:179:  3. Protecting Users' Legal Rights From Anti-Circumvention Law.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:192:users, your or third parties' legal rights to forbid circumvention of
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/COPYING:240:used to limit the access or legal rights of the compilation's users
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/CREDITS:35:it a lot easier for TeX users to cope with multiple or complex languages,
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/CREDITS:201:users, enthusiasts and software developers for their work in Indian
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/CREDITS:290:* Maxim Iorsh <iorsh AT users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/ChangeLog:440:      to Mac OS 10.6 users, who suffer from a bug.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20100919/ChangeLog:1494:     /pub/users/ucgadkw/indology/software/sinhala1-TeX.zip
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/ae_fonts_2.0/COPYING:16:software--to make sure the software is free for all its users.  This
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/ae_fonts_2.0/COPYING:110:    a warranty) and that users may redistribute the program under
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/AUTHORS:111:* Maxim Iorsh <iorsh AT users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:17:software for all its users.  We, the Free Software Foundation, use the
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:45:that there is no warranty for this free software.  For both users' and
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:50:  Some devices are designed to deny users access to install or run
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:53:protecting users' freedom to change the software.  The systematic
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:59:of the GPL, as needed to protect the freedom of users.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:147:  The Corresponding Source need not include anything that users
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:179:  3. Protecting Users' Legal Rights From Anti-Circumvention Law.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:192:users, your or third parties' legal rights to forbid circumvention of
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/COPYING:240:used to limit the access or legal rights of the compilation's users
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/CREDITS:35:it a lot easier for TeX users to cope with multiple or complex languages,
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/CREDITS:200:users, enthusiasts and software developers for their work in Indian
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/CREDITS:288:* Maxim Iorsh <iorsh AT users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/ChangeLog:5157:       10.6 users, who suffer from a bug.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/ChangeLog:6670:       /pub/users/ucgadkw/indology/software/sinhala1-TeX.zip The hope is
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/INSTALL:21:Users of Debian GNU/Linux system will probably want to use the Debian package,
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/INSTALL:30:Users of KDE can install .ttf files on a per-user basis using the KDE
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/INSTALL:80: In order to use OpenType, users of Windows 95, 98 and NT 4.0 can
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/fonts/freefont-20120503/INSTALL:94:depending on whether they should be available to all users on your system
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/CHANGELOG.TXT:224:  - Fixed erase users pictures
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:200:software for all its users.  We, the Free Software Foundation, use the
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:228:that there is no warranty for this free software.  For both users' and
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:233:  Some devices are designed to deny users access to install or run
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:236:protecting users' freedom to change the software.  The systematic
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:242:of the GPL, as needed to protect the freedom of users.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:330:  The Corresponding Source need not include anything that users
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:362:  3. Protecting Users' Legal Rights From Anti-Circumvention Law.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:375:users, your or third parties' legal rights to forbid circumvention of
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/LICENSE.TXT:423:used to limit the access or legal rights of the compilation's users
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/README.md:20:All users are invited to migrate to [tc-lib-pdf](https://github.com/tecnickcom/tc-lib-pdf), the modern and modular successor.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:4268:      * @param mixed $subset if true embed only a subset of the font (stores only the information related to the used characters); if false embed full font; if 'default' uses the default value set using setFontSubsetting(). This option is valid only for TrueTypeUnicode fonts. If you want to enable users to change the document, set this parameter to false. If you subset the font, the person who receives your PDF would need to have your same font in order to make changes to your PDF. The file size of the PDF would also be smaller because you are embedding only part of a font.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:4538:      * @param mixed $subset if true embed only a subset of the font (stores only the information related to the used characters); if false embed full font; if 'default' uses the default value set using setFontSubsetting(). This option is valid only for TrueTypeUnicode fonts. If you want to enable users to change the document, set this parameter to false. If you subset the font, the person who receives your PDF would need to have your same font in order to make changes to your PDF. The file size of the PDF would also be smaller because you are embedding only part of a font.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:11035:     * @param array $permissions the set of permissions (specify the ones you want to block):<ul><li>print : Print the document;</li><li>modify : Modify the contents of the document by operations other than those controlled by 'fill-forms', 'extract' and 'assemble';</li><li>copy : Copy or otherwise extract text and graphics from the document;</li><li>annot-forms : Add or modify text annotations, fill in interactive form fields, and, if 'modify' is also set, create or modify interactive form fields (including signature fields);</li><li>fill-forms : Fill in existing interactive form fields (including signature fields), even if 'annot-forms' is not specified;</li><li>extract : Extract text and graphics (in support of accessibility to users with disabilities or for other purposes);</li><li>assemble : Assemble the document (insert, rotate, or delete pages and create bookmarks or thumbnail images), even if 'modify' is not set;</li><li>print-high : Print the document to a representation from which a faithful digital copy of the PDF content could be generated. When this is not set, printing is limited to a low-level representation of the appearance, possibly of degraded quality.</li><li>owner : (inverted logic - only for public-key) when set permits change of encryption and enables all other permissions.</li></ul>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/composer/installed.json:1096:                    "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/bacon/bacon-qr-code/src/Renderer/Image/SvgImageBackEnd.php:281:        $this->xmlWriter->writeAttribute('gradientUnits', 'userSpaceOnUse');
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:709:The diff might look very big but 95% of Guzzle users will be able to upgrade without modification.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1157:  caused problems for many users: they aren't PSR-4 compliant, require an
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1378:* Adding more information to ExceptionCollection exceptions so that users have more context, including a stack trace of
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1489:  Symfony users can still use the old version of Monolog.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/guzzlehttp/guzzle/UPGRADING.md:1049:    "description":"Provides access to Zendesk views, groups, tickets, ticket fields, and users",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/guzzlehttp/guzzle/src/Middleware.php:200:        // To be compatible with Guzzle 7.1.x we need to allow users to pass a MessageFormatter
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/DSNConfigurator.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/Exception.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:29: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/POP3.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/POP3.php:40: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:1118:     * will send the message to the users terminal if they are logged
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/LICENSE:18:free software--to make sure the software is free for all its users.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/LICENSE:61:effectively restrict the users of a free program by obtaining a
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/LICENSE:105:users' freedom, it does ensure that the user of a program that is
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/README.md:87:While installing the entire package manually or with Composer is simple, convenient, and reliable, you may want to include only vital files in your project. At the very least you will need [src/PHPMailer.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/PHPMailer.php). If you're using SMTP, you'll need [src/SMTP.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/SMTP.php), and if you're using POP-before SMTP (*very* unlikely!), you'll need [src/POP3.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/POP3.php). You can skip the [language](https://github.com/PHPMailer/PHPMailer/tree/master/language/) folder if you're not showing errors to users and can make do with English-only errors. If you're using XOAUTH2 you will need [src/OAuth.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/OAuth.php) as well as the Composer dependencies for the services you wish to authenticate with. Really, it's much easier to use Composer!
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/composer.json:16:            "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:10: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:179:    //Use this to interact with an API on the users behalf
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/ramsey/collection/src/DoubleEndedQueueInterface.php:157: * do so. Users of any `DoubleEndedQueueInterface` implementations that do allow
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/validator/ConstraintValidator.php:67:     * should only be displayed for technical users. Non-technical users
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:271:        $insertSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (?, ?, ?, ?)";
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:121:            'mysql' => "CREATE TABLE $this->table ($this->idCol VARBINARY(255) NOT NULL PRIMARY KEY, $this->dataCol MEDIUMBLOB NOT NULL, $this->lifetimeCol INTEGER UNSIGNED, $this->timeCol INTEGER UNSIGNED NOT NULL) ENGINE = InnoDB",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:122:            'sqlite' => "CREATE TABLE $this->table ($this->idCol TEXT NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:123:            'pgsql' => "CREATE TABLE $this->table ($this->idCol VARCHAR(255) NOT NULL PRIMARY KEY, $this->dataCol BYTEA NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:124:            'oci' => "CREATE TABLE $this->table ($this->idCol VARCHAR2(255) NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:125:            'sqlsrv' => "CREATE TABLE $this->table ($this->idCol VARCHAR(255) NOT NULL PRIMARY KEY, $this->dataCol VARBINARY(MAX) NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:261:        $insertSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :lifetime, :time)";
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/EventStreamResponse.php:18: * To broadcast events to multiple users at once, for long-running
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Request.php:817:        // the check for $this->session avoids malicious users trying to fake a session cookie with proper name
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:330:            'mysql' => "CREATE TABLE $this->table ($this->idCol VARBINARY(128) NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER UNSIGNED NOT NULL, $this->timeCol INTEGER UNSIGNED NOT NULL) ENGINE = InnoDB",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:331:            'sqlite' => "CREATE TABLE $this->table ($this->idCol TEXT NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:332:            'pgsql' => "CREATE TABLE $this->table ($this->idCol VARCHAR(128) NOT NULL PRIMARY KEY, $this->dataCol BYTEA NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:333:            'oci' => "CREATE TABLE $this->table ($this->idCol VARCHAR2(128) NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:334:            'sqlsrv' => "CREATE TABLE $this->table ($this->idCol VARCHAR(128) NOT NULL PRIMARY KEY, $this->dataCol VARBINARY(MAX) NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:871:                $sql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, EMPTY_BLOB(), :expiry, :time) RETURNING $this->dataCol into :data";
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:877:                $sql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :expiry, :time)";
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:881:                $sql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :expiry, :time)";
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:934:                $mergeSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :expiry, :time) ".
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:948:                $mergeSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (:id, :data, :expiry, :time) ".
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/symfony/polyfill-ctype/README.md:4:This component provides `ctype_*` functions to users who run php versions without the ctype extension.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/psr/http-message/src/ServerRequestInterface.php:36: * content, matching authorization headers to users, etc). These parameters
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/psr/http-message/src/UriInterface.php:258:     * Users can provide both encoded and decoded path characters.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/psr/http-message/src/UriInterface.php:273:     * Users can provide both encoded and decoded query characters.
/home/ec2-user/orion/backup-20260919-174130/kernell/vendor/psr/http-message/src/UriInterface.php:290:     * Users can provide both encoded and decoded fragment characters.
/home/ec2-user/orion/backup-20260920-044205/kernell/composer.lock:1060:                    "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/backup-20260920-044205/kernell/app/Core/Database/QueryBuilder.php:94:        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");
/home/ec2-user/orion/backup-20260920-044205/kernell/app/Services/DTE/DteTenantConfigurationWriter.php:66:        $sql = 'INSERT INTO tenant_dte_configurations
/home/ec2-user/orion/backup-20260920-044205/kernell/app/Services/Queue/QueueDispatcher.php:32:            'INSERT INTO background_jobs
/home/ec2-user/orion/backup-20260920-044205/kernell/app/Services/Auth/LoginServide.php:30:            'SELECT * FROM usuarios WHERE email = :email AND activo = 1 LIMIT 1'
/home/ec2-user/orion/backup-20260920-044205/kernell/app/Services/Auth/LoginServide.php:39:        if (!password_verify($password, (string) ($user['password_hash'] ?? ''))) {
/home/ec2-user/orion/backup-20260920-044205/kernell/app/Services/Auth/RefreshTokenService.php:22:            'INSERT INTO user_sessions
/home/ec2-user/orion/backup-20260920-044205/kernell/app/Services/Auth/RefreshTokenService.php:45:             INNER JOIN usuarios u ON u.usuario_id = us.usuario_id
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/DSNConfigurator.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/Exception.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:29: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/POP3.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/POP3.php:40: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:1118:     * will send the message to the users terminal if they are logged
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/LICENSE:18:free software--to make sure the software is free for all its users.
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/LICENSE:61:effectively restrict the users of a free program by obtaining a
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/LICENSE:105:users' freedom, it does ensure that the user of a program that is
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/README.md:87:While installing the entire package manually or with Composer is simple, convenient, and reliable, you may want to include only vital files in your project. At the very least you will need [src/PHPMailer.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/PHPMailer.php). If you're using SMTP, you'll need [src/SMTP.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/SMTP.php), and if you're using POP-before SMTP (*very* unlikely!), you'll need [src/POP3.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/POP3.php). You can skip the [language](https://github.com/PHPMailer/PHPMailer/tree/master/language/) folder if you're not showing errors to users and can make do with English-only errors. If you're using XOAUTH2 you will need [src/OAuth.php](https://github.com/PHPMailer/PHPMailer/tree/master/src/OAuth.php) as well as the Composer dependencies for the services you wish to authenticate with. Really, it's much easier to use Composer!
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/composer.json:16:            "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:10: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:179:    //Use this to interact with an API on the users behalf
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:709:The diff might look very big but 95% of Guzzle users will be able to upgrade without modification.
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1157:  caused problems for many users: they aren't PSR-4 compliant, require an
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1378:* Adding more information to ExceptionCollection exceptions so that users have more context, including a stack trace of
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/guzzlehttp/guzzle/CHANGELOG.md:1489:  Symfony users can still use the old version of Monolog.
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/guzzlehttp/guzzle/UPGRADING.md:1049:    "description":"Provides access to Zendesk views, groups, tickets, ticket fields, and users",
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/guzzlehttp/guzzle/src/Middleware.php:200:        // To be compatible with Guzzle 7.1.x we need to allow users to pass a MessageFormatter
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/ramsey/collection/src/DoubleEndedQueueInterface.php:157: * do so. Users of any `DoubleEndedQueueInterface` implementations that do allow
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:271:        $insertSql = "INSERT INTO $this->table ($this->idCol, $this->dataCol, $this->lifetimeCol, $this->timeCol) VALUES (?, ?, ?, ?)";
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:121:            'mysql' => "CREATE TABLE $this->table ($this->idCol VARBINARY(255) NOT NULL PRIMARY KEY, $this->dataCol MEDIUMBLOB NOT NULL, $this->lifetimeCol INTEGER UNSIGNED, $this->timeCol INTEGER UNSIGNED NOT NULL) ENGINE = InnoDB",
/home/ec2-user/orion/backup-20260920-044205/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:122:            'sqlite' => "CREATE TABLE $this->table ($this->idCol TEXT NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
[ec2-user@ip-172-31-1-105 ~]$



[ec2-user@ip-172-31-1-105 ~]$ echo '===== MYSQL DATABASES ====='

sudo mysql -NBe 'SHOW DATABASES;' 2>/dev/null
===== MYSQL DATABASES =====
[ec2-user@ip-172-31-1-105 ~]$

