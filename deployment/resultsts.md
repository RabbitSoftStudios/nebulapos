 if (isset($m['password'])) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:110:                    $auth = rawurldecode($m['password']);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:112:                    $auth = [rawurldecode($m['user']), rawurldecode($m['password'])];
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:225:                throw new InvalidArgumentException('Invalid Redis DSN: the "auth" parameter must be a string, or a list of exactly two elements for ACL, "[username, password]".');
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:482:                $params['parameters']['password'] = $params['auth'][1];
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:484:                $params['parameters']['password'] = $params['auth'];
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:490:                    $sentinelPassword = $sentinelAuth[1];
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:493:                    $sentinelPassword = $sentinelAuth;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:497:                    $hosts[$i]['password'] ??= $sentinelPassword;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseBucketAdapter.php:74:        $dsnPattern = '/^(?<protocol>couchbase(?:s)?)\:\/\/(?:(?<username>[^\:]+)\:(?<password>[^\@]{6,})@)?'
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseBucketAdapter.php:82:            $password = $options['password'];
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseBucketAdapter.php:92:                $password = $matches['password'] ?: $password;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseBucketAdapter.php:109:            $client->authenticateAs($username, $password);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseBucketAdapter.php:113:            unset($options['username'], $options['password']);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseBucketAdapter.php:150:        $options['password'] ??= '';
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseCollectionAdapter.php:68:            $password = $options['password'] ?? '';
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseCollectionAdapter.php:78:                $password = isset($params['pass']) ? rawurldecode($params['pass']) : $password;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/CouchbaseCollectionAdapter.php:96:            $clusterOptions->credentials($username, $password);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/MemcachedAdapter.php:102:            $password = $options['password'] ?? null;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/MemcachedAdapter.php:112:                $params = preg_replace_callback('#^memcached:(//)?(?:([^@]*+)@)?#', static function ($m) use (&$username, &$password) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/MemcachedAdapter.php:114:                        [$username, $password] = explode(':', $m[2], 2) + [1 => null];
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/MemcachedAdapter.php:116:                        $password = null !== $password ? rawurldecode($password) : null;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/MemcachedAdapter.php:173:            unset($options['persistent_id'], $options['username'], $options['password'], $options['weight'], $options['lazy']);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/MemcachedAdapter.php:221:            if (null !== $username || null !== $password) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/MemcachedAdapter.php:225:                $client->setSaslAuthData($username, $password);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:34:    private ?string $password = null;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:50:     *  * db_password: The password when lazy-connect [default: '']
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:84:        $this->password = $options['db_password'] ?? $this->password;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:378:            $this->conn = new \PDO($this->dsn, $this->username, $this->password, $this->connectionOptions);
/home/ec2-user/orion/kernell/vendor/symfony/yaml/composer.json:11:            "email": "fabien@symfony.com"
/home/ec2-user/orion/kernell/vendor/symfony/routing/composer.json:11:            "email": "fabien@symfony.com"
/home/ec2-user/orion/kernell/vendor/symfony/routing/Attribute/Route.php:36:     * @param string|array<string,string>|null                  $path         The route path (i.e. "/user/login")
/home/ec2-user/orion/kernell/vendor/symfony/routing/Attribute/Route.php:37:     * @param string|null                                       $name         The route name (i.e. "app_user_login")
/home/ec2-user/orion/kernell/vendor/symfony/cache-contracts/composer.json:11:            "email": "p@tchwork.com"
/home/ec2-user/orion/kernell/vendor/symfony/polyfill-deepclone/composer.json:11:            "email": "p@tchwork.com"
/home/ec2-user/orion/kernell/vendor/symfony/polyfill-php83/bootstrap.php:53:    function ldap_connect_wallet(?string $uri, string $wallet, string $password, int $auth_mode = \GSLC_SSL_NO_AUTH) { return ldap_connect($uri, $wallet, $password, $auth_mode); }
/home/ec2-user/orion/kernell/vendor/symfony/polyfill-php83/bootstrap81.php:39:    function ldap_connect_wallet(?string $uri, string $wallet, #[\SensitiveParameter] string $password, int $auth_mode = \GSLC_SSL_NO_AUTH): \LDAP\Connection|false { return ldap_connect($uri, $wallet, $password, $auth_mode); }
/home/ec2-user/orion/kernell/vendor/symfony/polyfill-php83/composer.json:11:            "email": "p@tchwork.com"
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/composer.json:4:    "description": "PHPMailer is a full-featured email creation and transfer class for PHP",
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/composer.json:8:            "email": "phpmailer@synchromedia.co.uk"
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/composer.json:12:            "email": "jimjag@gmail.com"
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/composer.json:16:            "email": "codeworxtech@users.sourceforge.net"
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/composer.json:52:        "ext-mbstring": "Needed to send email in multibyte encoding charset or decode encoded addresses",
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:4: * PHPMailer - PHP email creation and transport class.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:10: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/get_oauth_token.php:179:    //Use this to interact with an API on the users behalf
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-nb.php:8: $PHPMAILER_LANG['authenticate']         = 'SMTP-feil: Kunne ikke autentiseres.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-nl.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP-fout: authenticatie mislukt.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-pl.php:8:$PHPMAILER_LANG['authenticate']         = 'Błąd SMTP: Nie można przeprowadzić uwierzytelnienia.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-pl.php:24:$PHPMAILER_LANG['provide_address']      = 'Należy podać prawidłowy adres email odbiorcy.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-pt.php:9:$PHPMAILER_LANG['authenticate']         = 'Erro SMTP: Falha na autenticação.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-pt_br.php:13:$PHPMAILER_LANG['authenticate']         = 'Erro de SMTP: Não foi possível autenticar.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ro.php:8:$PHPMAILER_LANG['authenticate']         = 'Eroare SMTP: Autentificarea a eșuat.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ro.php:20:$PHPMAILER_LANG['invalid_address']      = 'Adresa de email nu este validă: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ro.php:25:$PHPMAILER_LANG['provide_address']      = 'Trebuie să adăugați cel puțin o adresă de email.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ro.php:26:$PHPMAILER_LANG['recipients_failed']    = 'Eroare SMTP: Următoarele adrese de email au eșuat: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ro.php:27:$PHPMAILER_LANG['signing']              = 'A aparut o problemă la semnarea emailului. ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ru.php:11:$PHPMAILER_LANG['authenticate']         = 'Ошибка SMTP: не удалось пройти аутентификацию.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ru.php:23:$PHPMAILER_LANG['invalid_address']      = 'Не отправлено из-за неправильного формата email-адреса: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-si.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP දෝෂය: සත්‍යාපනය අසාර්ථක විය.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-si.php:10:$PHPMAILER_LANG['buggy_php']            = 'ඔබගේ PHP version එකෙහි පවතින දෝෂයක් නිසා email පණිවිඩ දෝෂ සහගත වීමේ හැකියාවක් ඇත. මෙය විසදීම සදහා SMTP භාවිතා කිරීම, mail.add_x_header INI setting එක අක්‍රීය කිරීම, MacOS හෝ Linux වලට මාරු වීම, හෝ ඔබගේ PHP version එක 7.0.17+ හෝ 7.1.3+ වලට අලුත් කිරීම කරගන්න.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sk.php:10:$PHPMAILER_LANG['authenticate']         = 'SMTP Error: Chyba autentifikácie.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sk.php:19:$PHPMAILER_LANG['instantiate']          = 'Nedá sa vytvoriť inštancia emailovej funkcie.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sk.php:20:$PHPMAILER_LANG['invalid_address']      = 'Neodoslané, emailová adresa je nesprávna: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sk.php:23:$PHPMAILER_LANG['mailer_not_supported'] = ' emailový klient nieje podporovaný.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sk.php:24:$PHPMAILER_LANG['provide_address']      = 'Musíte zadať aspoň jednu emailovú adresu príjemcu.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sl.php:11:$PHPMAILER_LANG['authenticate']         = 'SMTP napaka: Avtentikacija ni uspela.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sr.php:10:$PHPMAILER_LANG['authenticate']         = 'SMTP грешка: аутентификација није успела.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sr_latn.php:10:$PHPMAILER_LANG['authenticate']         = 'SMTP greška: autentifikacija nije uspela.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-sv.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP fel: Kunde inte autentisera.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-tl.php:10:$PHPMAILER_LANG['authenticate']         = 'SMTP Error: Hindi mapatotohanan.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-tl.php:22:$PHPMAILER_LANG['provide_address']      = 'Kailangan mong magbigay ng kahit isang email address na tatanggap.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-tr.php:13:$PHPMAILER_LANG['authenticate']         = 'SMTP Hatası: Oturum açılamadı.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-uk.php:10:$PHPMAILER_LANG['authenticate']         = 'Помилка SMTP: помилка авторизації.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-uk.php:19:$PHPMAILER_LANG['provide_address']      = 'Будь ласка, введіть хоча б одну email-адресу отримувача.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-uk.php:23:$PHPMAILER_LANG['invalid_address']      = 'Не відправлено через неправильний формат email-адреси: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ur.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP خرابی: تصدیق کرنے سے قاصر۔';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-vi.php:9:$PHPMAILER_LANG['authenticate']         = 'Lỗi SMTP: Không thể xác thực.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-zh.php:11:$PHPMAILER_LANG['authenticate']         = 'SMTP 錯誤：登入失敗。';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-zh_cn.php:11:$PHPMAILER_LANG['authenticate']         = 'SMTP 错误：登录失败。';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-af.php:8:$PHPMAILER_LANG['authenticate']         = 'SMTP-fout: kon nie geverifieer word nie.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ar.php:9:$PHPMAILER_LANG['authenticate']         = 'خطأ SMTP : لا يمكن تأكيد الهوية.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-as.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP ত্ৰুটি: প্ৰমাণীকৰণ কৰিব নোৱাৰি';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-az.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP xətası: Giriş uğursuz oldu.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ba.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP Greška: Neuspjela prijava.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-be.php:9:$PHPMAILER_LANG['authenticate']         = 'Памылка SMTP: памылка ідэнтыфікацыі.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-be.php:19:$PHPMAILER_LANG['invalid_address']      = 'Нельга даслаць паведамленне, няправільны email атрымальніка: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-be.php:20:$PHPMAILER_LANG['provide_address']      = 'Запоўніце, калі ласка, правільны email атрымальніка.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-bg.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP грешка: Не може да се удостовери пред сървъра.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-bg.php:21:$PHPMAILER_LANG['provide_address']      = 'Трябва да предоставите поне един email адрес за получател.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-bn.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP ত্রুটি: প্রমাণীকরণ করতে অক্ষম৷';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ca.php:9:$PHPMAILER_LANG['authenticate']         = 'Error SMTP: No s’ha pogut autenticar.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ca.php:19:$PHPMAILER_LANG['invalid_address']      = 'Adreça d’email invalida: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ca.php:21:$PHPMAILER_LANG['provide_address']      = 'S’ha de proveir almenys una adreça d’email com a destinatari.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-cs.php:8:$PHPMAILER_LANG['authenticate']         = 'Chyba SMTP: Autentizace selhala.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-cs.php:17:$PHPMAILER_LANG['instantiate']          = 'Nelze vytvořit instanci emailové funkce.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-cs.php:22:$PHPMAILER_LANG['provide_address']      = 'Musíte zadat alespoň jednu emailovou adresu příjemce.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-da.php:11:$PHPMAILER_LANG['authenticate']         = 'SMTP fejl: Login mislykkedes.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-da.php:22:$PHPMAILER_LANG['instantiate']          = 'Email funktionen kunne ikke initialiseres.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-da.php:28:$PHPMAILER_LANG['provide_address']      = 'Indtast mindst en modtagers email adresse.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-de.php:8:$PHPMAILER_LANG['authenticate']         = 'SMTP-Fehler: Authentifizierung fehlgeschlagen.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-el.php:8:$PHPMAILER_LANG['authenticate']         = 'Σφάλμα SMTP: Αδυναμία πιστοποίησης.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-eo.php:8:$PHPMAILER_LANG['authenticate']         = 'Eraro de servilo SMTP : aŭtentigo malsukcesis.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-es.php:11:$PHPMAILER_LANG['authenticate']         = 'Error SMTP: Imposible autentificar.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-es.php:23:$PHPMAILER_LANG['invalid_address']      = 'Imposible enviar: dirección de email inválido: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-es.php:28:$PHPMAILER_LANG['provide_address']      = 'Debe proporcionar al menos una dirección de email de destino.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-et.php:10:$PHPMAILER_LANG['authenticate']         = 'SMTP Viga: Autoriseerimise viga.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-fa.php:10:$PHPMAILER_LANG['authenticate']         = 'خطای SMTP: احراز هویت با شکست مواجه شد.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-fi.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP-virhe: käyttäjätunnistus epäonnistui.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-fo.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP feilur: Kundi ikki góðkenna.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-fo.php:21:$PHPMAILER_LANG['provide_address']      = 'Tú skal uppgeva minst móttakara-emailadressu(r).';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-fr.php:11:$PHPMAILER_LANG['authenticate']         = 'Erreur SMTP : échec de l’authentification.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-gl.php:9:$PHPMAILER_LANG['authenticate']         = 'Erro SMTP: Non puido ser autentificado.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-gl.php:19:$PHPMAILER_LANG['invalid_address']      = 'Non puido envia-lo correo: dirección de email inválida: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-gl.php:21:$PHPMAILER_LANG['provide_address']      = 'Debe engadir polo menos unha dirección de email coma destino.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-he.php:9:$PHPMAILER_LANG['authenticate']         = 'שגיאת SMTP: פעולת האימות נכשלה.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-hi.php:10:$PHPMAILER_LANG['authenticate']         = 'SMTP त्रुटि: प्रामाणिकता की जांच नहीं हो सका। ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-hr.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP Greška: Neuspjela autentikacija.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-hu.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP hiba: az azonosítás sikertelen.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-hy.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP -ի սխալ: չհաջողվեց ստուգել իսկությունը.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-id.php:11:$PHPMAILER_LANG['authenticate']         = 'Kesalahan SMTP: Tidak dapat mengotentikasi.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-it.php:10:$PHPMAILER_LANG['authenticate']         = 'SMTP Error: Impossibile autenticarsi.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-it.php:20:$PHPMAILER_LANG['invalid_address']      = 'Impossibile inviare, l\'indirizzo email non è valido: ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ja.php:12:$PHPMAILER_LANG['authenticate']         = 'SMTPエラー: 認証できませんでした。';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ka.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP შეცდომა: ავტორიზაცია შეუძლებელია.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ko.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP 오류: 인증할 수 없습니다.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ku.php:9:$PHPMAILER_LANG['authenticate']         = 'هەڵەی SMTP : نەتوانرا کۆدەکە پشتڕاست بکرێتەوە ';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-lt.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP klaida: autentifikacija nepavyko.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-lv.php:9:$PHPMAILER_LANG['authenticate']         = 'SMTP kļūda: Autorizācija neizdevās.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-mg.php:9:$PHPMAILER_LANG['authenticate']         = 'Hadisoana SMTP: Tsy nahomby ny fanamarinana.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-mn.php:9:$PHPMAILER_LANG['authenticate']         = 'Алдаа SMTP: Холбогдож чадсангүй.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/language/phpmailer.lang-ms.php:9:$PHPMAILER_LANG['authenticate']         = 'Ralat SMTP: Tidak dapat pengesahan.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/DSNConfigurator.php:4: * PHPMailer - PHP email creation and transport class.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/DSNConfigurator.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/DSNConfigurator.php:161:            $mailer->Password = $config['pass'];
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/DSNConfigurator.php:180:        unset($allowedOptions['Password']);
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/Exception.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:4: * PHPMailer - PHP email creation and transport class.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:53:     * The user's email address, usually used as the login ID
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:54:     * and also the from address when sending email.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:58:    protected $oauthUserEmail = '';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:90:        $this->oauthUserEmail = $options['userName'];
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:115:            ['refresh_token' => $this->oauthRefreshToken]
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuth.php:133:            $this->oauthUserEmail .
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php:4: * PHPMailer - PHP email creation and transport class.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php:29: * @see     SMTP::authenticate()
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/OAuthTokenProvider.php:39:     * "user=<user_email_address>\001auth=Bearer <access_token>\001\001"
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:4: * PHPMailer - PHP email creation and transport class.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:25: * PHPMailer - PHP email creation and transport class.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:29: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:64:     * Email priority.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:102:     * The From email address for the message.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:142:     * This body can be read by mail clients that do not have HTML email
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:232:     * The email address that a reading confirmation should be sent to, also known as read receipt.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:321:     * Uses the Username and Password properties.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:324:     * @see PHPMailer::$Password
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:345:     * SMTP password.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:349:    public $Password = '';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:352:     * SMTP authentication type. Options are CRAM-MD5, LOGIN, PLAIN, XOAUTH2.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:492:     * Usually the email address used as the source of the email.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:550:     * The function that handles the result of the send email action.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:551:     * It is called out by send() for each email sent.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:557:     *   array   $to            email addresses of the recipients
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:558:     *   array   $cc            cc email addresses
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:559:     *   array   $bcc           bcc email addresses
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:561:     *   string  $body          the email body
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:562:     *   string  $from          email address of sender
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:579:     * Which validator to use by default when validating email addresses.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:581:     * The default validator uses PHP's FILTER_VALIDATE_EMAIL filter_var option.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:745:     * The S/MIME password for the key.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1023:     * @param string $address The email address to send to
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1038:     * @param string $address The email address to send to
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1053:     * @param string $address The email address to send to
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1068:     * @param string $address The email address to reply to
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1087:     * @param string $address The email address
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1169:     * @param string $address The email address to send, resp. to reply to
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1233:     * Parse and validate a string containing one or more RFC822-style comma-separated email addresses
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1296:                    list($name, $email) = explode('<', $address);
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1297:                    $email = trim(str_replace('>', '', $email));
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1299:                    if (static::validateAddress($email)) {
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1314:                            'address' => $email,
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1369:     * Return the Message-ID header of the last email.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1382:     * Check that a string looks like an email address.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1387:     * * `php` Use PHP built-in FILTER_VALIDATE_EMAIL;
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1388:     * * `html5` Use the pattern given by the HTML5 spec for 'email' type form input elements.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1389:     * * `eai` Use a pattern similar to the HTML5 spec for 'email' and to firefox, extended to support EAI (RFC6530).
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1401:     * @param string          $address       The email address to check
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1423:                 * A more complex and more permissive version of the RFC5322 regex on which FILTER_VALIDATE_EMAIL
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1451:                 * This is the pattern used in the HTML5 spec for validation of 'email' type form input elements.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1453:                 * @see https://html.spec.whatwg.org/#e-mail-state-(type=email)
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1462:                 * This is the pattern used in the HTML5 spec for validation of 'email' type
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1463:                 * form input elements (as above), modified to accept Unicode email addresses.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1465:                 * 'eai' is an acronym for Email Address Internationalization.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1469:                 * @see https://html.spec.whatwg.org/#e-mail-state-(type=email)
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1470:                 * @see https://en.wikipedia.org/wiki/International_email
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1480:                return filter_var($address, FILTER_VALIDATE_EMAIL) !== false;
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1496:     * Converts IDN in given email address to its ASCII form, also known as punycode, if possible.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1505:     * @param string $address The email address to convert
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1796:        //Sendmail docs: https://www.sendmail.org/~ca/email/man/sendmail.html
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:1992:        //Sendmail docs: https://www.sendmail.org/~ca/email/man/sendmail.html
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:2320:                        $this->SMTPAuth && !$this->smtp->authenticate(
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:2322:                            $this->Password,
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:2327:                        throw new Exception($this->lang('authenticate'));
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:2397:            'authenticate' => 'SMTP Error: Could not authenticate.',
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:2416:            'provide_address' => 'You must provide at least one recipient email address.',
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:2473:                    //$PHPMAILER_LANG['authenticate'] = 'SMTP-Fehler: Authentifizierung fehlgeschlagen.';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:4491:     * If you don't provide a $basedir, relative paths will be left untouched (and thus probably break in email)
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:4592:            $this->AltBody = 'This is an HTML-only message. To view it, activate HTML in your email application.'
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:4944:     * Set the public and private key files and password for S/MIME signing.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:4948:     * @param string $key_pass            Password for private key
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:29: *   to send a batch of emails then just perform the authentication once at the start,
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:40: * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:106:     * POP3 password.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:110:    public $password;
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:167:     * @param string   $password
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:177:        $password = '',
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:182:        return $pop->authorise($host, $port, $timeout, $username, $password, $debug_level);
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:186:     * Authenticate with a POP3 server.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:187:     * A connect, login, disconnect sequence
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:194:     * @param string   $password
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:199:    public function authorise($host, $port = false, $timeout = false, $username = '', $password = '', $debug_level = 0)
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:216:        $this->password = $password;
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:222:            $login_result = $this->login($this->username, $this->password);
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:223:            if ($login_result) {
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:229:        //We need to disconnect regardless of whether the login succeeded
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:305:     * @param string $password
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:309:    public function login($username = '', $password = '')
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:318:        if (empty($password)) {
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:319:            $password = $this->password;
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:326:            //Send the Password
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/POP3.php:327:            $this->sendString("PASS $password" . static::LE);
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:4: * PHPMailer RFC821 SMTP email transport class.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:11: * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:25: * PHPMailer RFC821 SMTP email transport class.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:219:        'NAME', 'ADDR', 'PORT', 'PROTO', 'HELO', 'LOGIN', 'DESTADDR', 'DESTPORT'
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:521:     * @param string $password The password
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:522:     * @param string $authtype The auth type (CRAM-MD5, PLAIN, LOGIN, XOAUTH2)
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:525:     * @return bool True if successfully authenticated
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:527:    public function authenticate(
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:529:        $password,
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:564:                foreach (['CRAM-MD5', 'LOGIN', 'PLAIN', 'XOAUTH2'] as $method) {
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:584:            $authtype = 'LOGIN';
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:592:                //Send encoded username and password
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:597:                        'User & Password',
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:598:                        base64_encode("\0" . $username . "\0" . $password),
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:605:            case 'LOGIN':
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:607:                if (!$this->sendCommand('AUTH', 'AUTH LOGIN', 334)) {
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:613:                if (!$this->sendCommand('Password', base64_encode($password), 235)) {
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:626:                $response = $username . ' ' . $this->hmac($challenge, $password);
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:921:     * Starts a mail transaction from the email address specified in
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:1114:     * Starts a mail transaction from the email address specified in $from.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:1118:     * will send the message to the users terminal if they are logged
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:1119:     * in and send them an email.
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/SMTP.php:1185:            in_array($command, ['User & Password', 'Username', 'Password'], true)
/home/ec2-user/orion/kernell/vendor/guzzlehttp/promises/composer.json:11:            "email": "hello@gjcampbell.co.uk",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/promises/composer.json:16:            "email": "mtdowling@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/promises/composer.json:21:            "email": "tobias.nyholm@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/promises/composer.json:26:            "email": "webmaster@tubo-world.de",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/composer.json:18:            "email": "hello@gjcampbell.co.uk",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/composer.json:23:            "email": "mtdowling@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/composer.json:28:            "email": "gmponos@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/composer.json:33:            "email": "tobias.nyholm@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/composer.json:38:            "email": "mark.sagikazar@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/composer.json:43:            "email": "webmaster@tubo-world.de",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/composer.json:48:            "email": "mark.sagikazar@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/src/Uri.php:547:    public function withUserInfo($user, $password = null): UriInterface
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/src/Uri.php:550:        if ($password !== null) {
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/src/Uri.php:551:            $info .= ':'.$this->filterUserInfoComponent($password);
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/src/Utils.php:481:     * Redact the password in the user info part of a URI.
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/src/MimeType.php:907:        'see' => 'application/vnd.seemail',
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/composer.json:19:            "email": "hello@gjcampbell.co.uk",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/composer.json:24:            "email": "mtdowling@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/composer.json:29:            "email": "jeremeamia@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/composer.json:34:            "email": "gmponos@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/composer.json:39:            "email": "tobias.nyholm@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/composer.json:44:            "email": "mark.sagikazar@gmail.com",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/composer.json:49:            "email": "webmaster@tubo-world.de",
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/src/Middleware.php:200:        // To be compatible with Guzzle 7.1.x we need to allow users to pass a MessageFormatter
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/src/RequestOptions.php:43:     * The array must contain the username in index [0], the password in index

===== REFERENCIAS A DATABASE =====
/home/ec2-user/orion/kernell/vendor/monolog/monolog/composer.json:3:    "description": "Sends your logs to files, sockets, inboxes, databases and various web services",
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Level.php:74:     * Example: Entire website down, database unavailable, etc.
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Logger.php:90:     * Example: Entire website down, database unavailable, etc.
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Formatter/JsonFormatter.php:21: * This can be useful to log to databases or remote APIs
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Handler/DeduplicationHandler.php:34: * a major component failure like a database server being down which makes all requests fail in the
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Handler/MongoDBHandler.php:24: * Logs to a MongoDB database.
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Handler/MongoDBHandler.php:48:     * @param string         $database   Database name
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Handler/MongoDBHandler.php:51:    public function __construct(Client|Manager $mongodb, string $database, string $collection, int|string|Level $level = Level::Debug, bool $bubble = true)
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Handler/MongoDBHandler.php:54:            $this->collection = method_exists($mongodb, 'getCollection') ? $mongodb->getCollection($database, $collection) : $mongodb->selectCollection($database, $collection);
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Handler/MongoDBHandler.php:57:            $this->namespace = $database . '.' . $collection;
/home/ec2-user/orion/kernell/vendor/monolog/monolog/src/Monolog/Handler/PushoverHandler.php:76:        'persistent', 'echo', 'updown', 'none',
/home/ec2-user/orion/kernell/vendor/tecnickcom/tcpdf/composer.json:60:            "/.phpdoc",
/home/ec2-user/orion/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:7732:           $tempdoc = TCPDF_STATIC::getObjFilename('doc', $this->file_id);
/home/ec2-user/orion/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:7733:           $f = TCPDF_STATIC::fopenLocal($tempdoc, 'wb');
/home/ec2-user/orion/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:7735:           $this->Error('Unable to create temporary file: '.$tempdoc);
/home/ec2-user/orion/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:7743:           openssl_pkcs7_sign($tempdoc, $tempsign, $this->signature_data['signcert'], array($this->signature_data['privkey'], $this->signature_data['password']), array(), PKCS7_BINARY | PKCS7_DETACHED);
/home/ec2-user/orion/kernell/vendor/tecnickcom/tcpdf/tcpdf.php:7745:           openssl_pkcs7_sign($tempdoc, $tempsign, $this->signature_data['signcert'], array($this->signature_data['privkey'], $this->signature_data['password']), array(), PKCS7_BINARY | PKCS7_DETACHED, $this->signature_data['extracerts']);
/home/ec2-user/orion/kernell/vendor/vlucas/phpdotenv/composer.json:2:    "name": "vlucas/phpdotenv",
/home/ec2-user/orion/kernell/vendor/vlucas/phpdotenv/src/Dotenv.php:208:        $phpdotenv = new self(new StringStore($content), new Parser(), new Loader(), $repository);
/home/ec2-user/orion/kernell/vendor/vlucas/phpdotenv/src/Dotenv.php:210:        return $phpdotenv->load();
/home/ec2-user/orion/kernell/vendor/vlucas/phpdotenv/src/Util/Str.php:57:         * @see https://github.com/vlucas/phpdotenv/issues/500
/home/ec2-user/orion/kernell/vendor/psr/log/src/LoggerInterface.php:32:     * Example: Entire website down, database unavailable, etc. This should
/home/ec2-user/orion/kernell/vendor/psr/log/src/LoggerTrait.php:26:     * Example: Entire website down, database unavailable, etc. This should
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/MongoDbSessionHandler.php:40:     *  * database: The name of the database [required]
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/MongoDbSessionHandler.php:50:     * the sessions in the database as described below:
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/MongoDbSessionHandler.php:65:     * @throws \InvalidArgumentException When "database" or "collection" not provided
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/MongoDbSessionHandler.php:69:        if (!isset($options['database']) || !isset($options['collection'])) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/MongoDbSessionHandler.php:70:            throw new \InvalidArgumentException('You must provide the "database" and "collection" option for MongoDBSessionHandler.');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/MongoDbSessionHandler.php:78:        $this->namespace = $options['database'].'.'.$options['collection'];
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:24: * Session handler using a PDO connection to read and write data.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:26: * It works with MySQL, PostgreSQL, Oracle, SQL Server and SQLite and implements
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:34: * Attention: Since SQLite does not support row level locks but locks the whole database,
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:36: * for another to finish. So saving session in SQLite should only be considered for
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:40: * For this reason it must be saved in a binary column in the database like BLOB in MySQL.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:50:class PdoSessionHandler extends AbstractSessionHandler
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:62:     * lock is not enforced by the database and thus other, unaware parts of the
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:65:     * This mode is not available for SQLite and not yet implemented for oci and sqlsrv.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:71:     * closing a session, you have to be careful when you use same database connection
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:77:    private \PDO $pdo;
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:119:     * @var \PDOStatement[] An array of statements to release advisory locks
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:139:     * You can either pass an existing database connection as PDO instance or
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:140:     * pass a DSN string that will be used to lazy-connect to the database
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:142:     * which will then use the session.save_path ini setting as PDO DSN parameter.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:150:     *  * db_username: The username when lazy-connect [default: '']
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:156:     * @param \PDO|string|null $pdoOrDsn A \PDO instance or DSN string or URL string or null
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:158:     * @throws \InvalidArgumentException When PDO error mode is not PDO::ERRMODE_EXCEPTION
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:160:    public function __construct(#[\SensitiveParameter] \PDO|string|null $pdoOrDsn = null, #[\SensitiveParameter] array $options = [])
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:162:        if ($pdoOrDsn instanceof \PDO) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:163:            if (\PDO::ERRMODE_EXCEPTION !== $pdoOrDsn->getAttribute(\PDO::ATTR_ERRMODE)) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:164:                throw new \InvalidArgumentException(\sprintf('"%s" requires PDO error mode attribute be set to throw Exceptions (i.e. $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)).', __CLASS__));
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:167:            $this->pdo = $pdoOrDsn;
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:168:            $this->driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:169:        } elseif (\is_string($pdoOrDsn) && str_contains($pdoOrDsn, '://')) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:170:            $this->dsn = $this->buildDsnFromUrl($pdoOrDsn);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:172:            $this->dsn = $pdoOrDsn;
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:180:        $this->username = $options['db_username'] ?? $this->username;
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:192:    public function configureSchema(Schema $schema, ?\Closure $isSameDatabase = null)
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:194:        if ($schema->hasTable($this->table) || ($isSameDatabase && !$isSameDatabase($this->getConnection()->exec(...)))) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:220:            case 'sqlite':
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:249:                throw new \DomainException(\sprintf('Creating the session table is currently not implemented for PDO driver "%s".', $this->driver));
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:271:            case 'sqlite':
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:296:                throw new \DomainException(\sprintf('Creating the session table is currently not implemented for PDO driver "%s".', $this->driver));
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:316:     * @throws \PDOException    When the table already exists
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:317:     * @throws \DomainException When an unsupported PDO driver is used
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:331:            'sqlite' => "CREATE TABLE $this->table ($this->idCol TEXT NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:335:            default => throw new \DomainException(\sprintf('Creating the session table is currently not implemented for PDO driver "%s".', $this->driver)),
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:339:            $this->pdo->exec($sql);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:340:            $this->pdo->exec("CREATE INDEX {$this->lifetimeCol}_idx ON $this->table ($this->lifetimeCol)");
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:341:        } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:362:        if (!isset($this->pdo)) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:373:        } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:395:            $stmt = $this->pdo->prepare($sql);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:396:            $stmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:398:        } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:412:            // We use a single MERGE SQL query when supported by the database.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:423:            // When MERGE is not supported, like in Postgres < 9.5, we have to use this approach that can result in
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:432:                } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:433:                    // Handle integrity violation SQLSTATE 23000 (or a subclass like 23505 in Postgres) for duplicate keys
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:441:        } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:455:            $updateStmt = $this->pdo->prepare(
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:458:            $updateStmt->bindValue(':id', $sessionId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:459:            $updateStmt->bindValue(':expiry', $expiry, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:460:            $updateStmt->bindValue(':time', time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:462:        } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:484:            $stmt = $this->pdo->prepare($sql);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:485:            $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:490:            unset($this->pdo, $this->driver); // only close lazy-connection
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:497:     * Lazy-connects to the database.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:501:        $this->pdo = new \PDO($dsn, $this->username, $this->password, $this->connectionOptions);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:502:        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:503:        $this->driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:507:     * Builds a PDO DSN from a URL-like connection string.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:509:     * @todo implement missing support for oci DSN (which look totally different from other PDO ones)
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:513:        // (pdo_)?sqlite3?:///... => (pdo_)?sqlite3?://localhost/... or else the URL will be invalid
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:514:        $url = preg_replace('#^((?:pdo_)?sqlite3?):///#', '$1://localhost/', $dsnOrUrl);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:534:            throw new \InvalidArgumentException('URLs without scheme are not supported to configure the PdoSessionHandler.');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:540:            'postgres' => 'pgsql',
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:541:            'postgresql' => 'pgsql',
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:542:            'sqlite3' => 'sqlite',
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:547:        // Doctrine DBAL supports passing its internal pdo_* driver names directly too (allowing both dashes and underscores). This allows supporting the same here.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:548:        if (str_starts_with($driver, 'pdo_') || str_starts_with($driver, 'pdo-')) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:555:                $dsn = 'mysql:';
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:594:            case 'sqlite':
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:595:                return 'sqlite:'.substr($params['path'], 1);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:610:                    $dsn .= ';Database='.$dbName;
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:616:                throw new \InvalidArgumentException(\sprintf('The scheme "%s" is not supported by the PdoSessionHandler URL configuration. Pass a PDO DSN directly.', $params['scheme']));
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:623:     * Since SQLite does not support row level locks, we have to acquire a reserved lock
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:624:     * on the database immediately. Because of https://bugs.php.net/42766 we have to create
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:625:     * such a transaction manually which also means we cannot use PDO::commit or
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:626:     * PDO::rollback or PDO::inTransaction for SQLite.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:635:            if ('sqlite' === $this->driver) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:636:                $this->pdo->exec('BEGIN IMMEDIATE TRANSACTION');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:639:                    $this->pdo->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:641:                $this->pdo->beginTransaction();
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:655:                if ('sqlite' === $this->driver) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:656:                    $this->pdo->exec('COMMIT');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:658:                    $this->pdo->commit();
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:661:            } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:679:            if ('sqlite' === $this->driver) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:680:                $this->pdo->exec('ROLLBACK');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:682:                $this->pdo->rollBack();
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:701:        $selectStmt = $this->pdo->prepare($selectSql);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:702:        $selectStmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:707:            $sessionRows = $selectStmt->fetchAll(\PDO::FETCH_NUM);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:726:            if (!filter_var(\ini_get('session.use_strict_mode'), \FILTER_VALIDATE_BOOL) && self::LOCK_TRANSACTIONAL === $this->lockMode && 'sqlite' !== $this->driver) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:734:                } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:740:                        // aborted in PostgreSQL and disallow further queries within it.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:755:     * Executes an application-level lock on the database.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:757:     * @return \PDOStatement The statement that needs to be executed later to release the lock
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:759:     * @throws \DomainException When an unsupported PDO driver is used
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:765:    private function doAdvisoryLock(#[\SensitiveParameter] string $sessionId): \PDOStatement
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:773:                $stmt = $this->pdo->prepare('SELECT GET_LOCK(:key, 50)');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:774:                $stmt->bindValue(':key', $lockId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:777:                $releaseStmt = $this->pdo->prepare('DO RELEASE_LOCK(:key)');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:778:                $releaseStmt->bindValue(':key', $lockId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:789:                    $stmt = $this->pdo->prepare('SELECT pg_advisory_lock(:key1, :key2)');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:790:                    $stmt->bindValue(':key1', $sessionInt1, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:791:                    $stmt->bindValue(':key2', $sessionInt2, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:794:                    $releaseStmt = $this->pdo->prepare('SELECT pg_advisory_unlock(:key1, :key2)');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:795:                    $releaseStmt->bindValue(':key1', $sessionInt1, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:796:                    $releaseStmt->bindValue(':key2', $sessionInt2, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:800:                    $stmt = $this->pdo->prepare('SELECT pg_advisory_lock(:key)');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:801:                    $stmt->bindValue(':key', $sessionBigInt, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:804:                    $releaseStmt = $this->pdo->prepare('SELECT pg_advisory_unlock(:key)');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:805:                    $releaseStmt->bindValue(':key', $sessionBigInt, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:809:            case 'sqlite':
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:810:                throw new \DomainException('SQLite does not support advisory locks.');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:812:                throw new \DomainException(\sprintf('Advisory locks are currently not implemented for PDO driver "%s".', $this->driver));
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:836:     * @throws \DomainException When an unsupported PDO driver is used
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:850:                case 'sqlite':
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:854:                    throw new \DomainException(\sprintf('Transactional locks are currently not implemented for PDO driver "%s".', $this->driver));
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:862:     * Returns an insert statement supported by the database for writing session data.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:864:    private function getInsertStatement(#[\SensitiveParameter] string $sessionId, string $sessionData, int $maxlifetime): \PDOStatement
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:885:        $stmt = $this->pdo->prepare($sql);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:886:        $stmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:887:        $stmt->bindParam(':data', $data, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:888:        $stmt->bindValue(':expiry', time() + $maxlifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:889:        $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:895:     * Returns an update statement supported by the database for writing session data.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:897:    private function getUpdateStatement(#[\SensitiveParameter] string $sessionId, string $sessionData, int $maxlifetime): \PDOStatement
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:918:        $stmt = $this->pdo->prepare($sql);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:919:        $stmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:920:        $stmt->bindParam(':data', $data, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:921:        $stmt->bindValue(':expiry', time() + $maxlifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:922:        $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:928:     * Returns a merge/upsert (i.e. insert or update) statement when supported by the database for writing session data.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:930:    private function getMergeStatement(#[\SensitiveParameter] string $sessionId, string $data, int $maxlifetime): ?\PDOStatement
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:937:            case 'sqlsrv' === $this->driver && version_compare($this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION), '10', '>='):
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:944:            case 'sqlite' === $this->driver:
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:947:            case 'pgsql' === $this->driver && version_compare($this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION), '9.5', '>='):
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:956:        $mergeStmt = $this->pdo->prepare($mergeSql);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:963:            $mergeStmt->bindParam(1, $sessionId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:964:            $mergeStmt->bindParam(2, $sessionId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:965:            $mergeStmt->bindParam(3, $dataStream, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:966:            $mergeStmt->bindValue(4, time() + $maxlifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:967:            $mergeStmt->bindValue(5, time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:968:            $mergeStmt->bindParam(6, $dataStream, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:969:            $mergeStmt->bindValue(7, time() + $maxlifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:970:            $mergeStmt->bindValue(8, time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:972:            $mergeStmt->bindParam(':id', $sessionId, \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:973:            $mergeStmt->bindParam(':data', $data, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:974:            $mergeStmt->bindValue(':expiry', time() + $maxlifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:975:            $mergeStmt->bindValue(':time', time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:982:     * Return a PDO instance.
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:984:    protected function getConnection(): \PDO
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:986:        if (!isset($this->pdo)) {
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:990:        return $this->pdo;
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:48:            case $connection instanceof \PDO:
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:49:                return new PdoSessionHandler($connection);
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:72:            case str_starts_with($connection, 'pdo_oci://'):
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:74:                    throw new \InvalidArgumentException('Unsupported PDO OCI DSN. Try running "composer require doctrine/dbal".');
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:85:            case str_starts_with($connection, 'mysql://'):
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:88:            case str_starts_with($connection, 'postgres://'):
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:89:            case str_starts_with($connection, 'postgresql://'):
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:91:            case str_starts_with($connection, 'sqlite://'):
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:92:            case str_starts_with($connection, 'sqlite3://'):
/home/ec2-user/orion/kernell/vendor/symfony/http-foundation/Session/Storage/Handler/SessionHandlerFactory.php:93:                return new PdoSessionHandler($connection, $options);
/home/ec2-user/orion/kernell/vendor/symfony/validator/Mapping/Factory/LazyLoadingMetadataFactory.php:35: * filesystem or a database. If you want to use multiple loaders, wrap them in a
/home/ec2-user/orion/kernell/vendor/symfony/cache/LockRegistry.php:52:        __DIR__.\DIRECTORY_SEPARATOR.'Adapter'.\DIRECTORY_SEPARATOR.'PdoAdapter.php',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:477:                $params['parameters']['database'] = $params['dbindex'];
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RedisTrait.php:641:                // can hang your server when it is executed against large databases (millions of items).
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RelayProxy.php:43:    public function __construct($host = null, $port = 6379, $connect_timeout = 0.0, $command_timeout = 0.0, #[\SensitiveParameter] $context = [], $database = 0)
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RelayProxy.php:253:    public function connect($host, $port = 6379, $timeout = 0.0, $persistent_id = null, $retry_interval = 0, $read_timeout = 0.0, #[\SensitiveParameter] $context = [], $database = 0): bool
/home/ec2-user/orion/kernell/vendor/symfony/cache/Traits/RelayProxy.php:1063:    public function pconnect($host, $port = 6379, $timeout = 0.0, $persistent_id = null, $retry_interval = 0, $read_timeout = 0.0, #[\SensitiveParameter] $context = [], $database = 0): bool
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/AbstractAdapter.php:140:        if (preg_match('/^(mysql|oci|pgsql|sqlsrv|sqlite):/', $dsn)) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/AbstractAdapter.php:141:            return PdoAdapter::createConnection($dsn, $options);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/AbstractAdapter.php:144:        throw new InvalidArgumentException('Unsupported DSN: it does not start with "redis[s]:", "valkey[s]:", "memcached:", "couchbase:", "mysql:", "oci:", "pgsql:", "sqlsrv:" nor "sqlite:".');
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:23:use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:54:     * You can either pass an existing database Doctrine DBAL Connection or
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:55:     * a DSN string that will be used to connect to the database.
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:88:                'mssql' => 'pdo_sqlsrv',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:89:                'mysql' => 'pdo_mysql',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:90:                'mysql2' => 'pdo_mysql',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:91:                'postgres' => 'pdo_pgsql',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:92:                'postgresql' => 'pdo_pgsql',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:93:                'pgsql' => 'pdo_pgsql',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:94:                'sqlite' => 'pdo_sqlite',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:95:                'sqlite3' => 'pdo_sqlite',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:127:        foreach ($schema->toSql($this->conn->getDatabasePlatform()) as $sql) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:135:    public function configureSchema(Schema $schema, Connection $forConnection, \Closure $isSameDatabase)
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:141:        if ($forConnection !== $this->conn && !$isSameDatabase($this->conn->executeStatement(...))) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:219:            $sql = $this->conn->getDatabasePlatform()->getTruncateTableSQL($this->table);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:250:        if ($this->conn->isTransactionActive() && $this->conn->getDatabasePlatform()->supportsSavepoints()) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:290:            case 'sqlite':
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:307:            if (!$this->conn->isTransactionActive() || \in_array($platformName, ['pgsql', 'sqlite', 'sqlsrv'], true)) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:352:                if (!$this->conn->isTransactionActive() || \in_array($platformName, ['pgsql', 'sqlite', 'sqlsrv'], true)) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:391:        $platform = $this->conn->getDatabasePlatform();
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:395:            $sqlitePlatformClass = 'Doctrine\DBAL\Platforms\SQLitePlatform';
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:397:            $sqlitePlatformClass = 'Doctrine\DBAL\Platforms\SqlitePlatform';
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:402:            $platform instanceof $sqlitePlatformClass => 'sqlite',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:403:            $platform instanceof PostgreSQLPlatform => 'pgsql',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:425:            'sqlite' => 'text',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/DoctrineDbalAdapter.php:445:            'sqlite' => 'text',
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:19:class PdoAdapter extends AbstractAdapter implements PruneableInterface
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:24:    private \PDO $conn;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:39:     * You can either pass an existing database connection as PDO instance or
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:40:     * a DSN string that will be used to lazy-connect to the database when the
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:49:     *  * db_username: The username when lazy-connect [default: '']
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:53:     * @throws InvalidArgumentException When first argument is not PDO nor Connection nor string
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:54:     * @throws InvalidArgumentException When PDO error mode is not PDO::ERRMODE_EXCEPTION
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:57:    public function __construct(#[\SensitiveParameter] \PDO|string $connOrDsn, string $namespace = '', int $defaultLifetime = 0, array $options = [], ?MarshallerInterface $marshaller = null)
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:60:            throw new InvalidArgumentException(\sprintf('Usage of Doctrine DBAL URL with "%s" is not supported. Use a PDO DSN or "%s" instead.', __CLASS__, DoctrineDbalAdapter::class));
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:67:        if ($connOrDsn instanceof \PDO) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:68:            if (\PDO::ERRMODE_EXCEPTION !== $connOrDsn->getAttribute(\PDO::ATTR_ERRMODE)) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:69:                throw new InvalidArgumentException(\sprintf('"%s" requires PDO error mode attribute be set to throw Exceptions (i.e. $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)).', __CLASS__));
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:83:        $this->username = $options['db_username'] ?? $this->username;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:92:    public static function createConnection(#[\SensitiveParameter] string $dsn, array $options = []): \PDO|string
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:98:        $pdo = new \PDO($dsn);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:99:        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:101:        return $pdo;
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:110:     * @throws \PDOException    When the table already exists
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:111:     * @throws \DomainException When an unsupported PDO driver is used
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:122:            'sqlite' => "CREATE TABLE $this->table ($this->idCol TEXT NOT NULL PRIMARY KEY, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER, $this->timeCol INTEGER NOT NULL)",
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:126:            default => throw new \DomainException(\sprintf('Creating the cache table is currently not implemented for PDO driver "%s".', $driver)),
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:144:        } catch (\PDOException) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:147:        $delete->bindValue(':time', time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:150:            $delete->bindValue(':namespace', \sprintf('%s%%', $this->namespace), \PDO::PARAM_STR);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:154:        } catch (\PDOException) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:169:        $stmt->bindValue($i = 1, $now, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:178:            $stmt->setFetchMode(\PDO::FETCH_NUM);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:194:            $stmt->bindValue($i = 1, $now, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:210:        $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:221:            if ('sqlite' === $this->getDriver()) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:233:        } catch (\PDOException) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:246:        } catch (\PDOException) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:280:            case 'sqlite' === $driver:
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:296:        } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:297:            if ($this->isTableMissing($e) && (!$conn->inTransaction() || \in_array($driver, ['pgsql', 'sqlite', 'sqlsrv'], true))) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:307:            $stmt->bindParam(3, $data, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:308:            $stmt->bindValue(4, $lifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:309:            $stmt->bindValue(5, $now, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:310:            $stmt->bindParam(6, $data, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:311:            $stmt->bindValue(7, $lifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:312:            $stmt->bindValue(8, $now, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:315:            $stmt->bindParam(':data', $data, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:316:            $stmt->bindValue(':lifetime', $lifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:317:            $stmt->bindValue(':time', $now, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:323:            $insertStmt->bindParam(':data', $data, \PDO::PARAM_LOB);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:324:            $insertStmt->bindValue(':lifetime', $lifetime, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:325:            $insertStmt->bindValue(':time', $now, \PDO::PARAM_INT);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:341:            } catch (\PDOException $e) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:342:                if ($this->isTableMissing($e) && (!$conn->inTransaction() || \in_array($driver, ['pgsql', 'sqlite', 'sqlsrv'], true))) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:350:                } catch (\PDOException) {
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:375:    private function getConnection(): \PDO
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:378:            $this->conn = new \PDO($this->dsn, $this->username, $this->password, $this->connectionOptions);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:379:            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:387:        return $this->driver ??= $this->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:392:        return $this->serverVersion ??= $this->getConnection()->getAttribute(\PDO::ATTR_SERVER_VERSION);
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:395:    private function isTableMissing(\PDOException $exception): bool
/home/ec2-user/orion/kernell/vendor/symfony/cache/Adapter/PdoAdapter.php:402:            'sqlite' => str_contains($exception->getMessage(), 'no such table:'),
/home/ec2-user/orion/kernell/vendor/symfony/routing/Loader/Configurator/RoutesReference.php:14:// For the phpdoc to remain compatible with the generation of per-app Routes class,
/home/ec2-user/orion/kernell/vendor/symfony/routing/Loader/Configurator/RoutesReference.php:16:// the phpdoc need to be in the current namespace or be root-scoped.
/home/ec2-user/orion/kernell/vendor/symfony/polyfill-php83/Resources/stubs/SQLite3Exception.php:13:    class SQLite3Exception extends Exception
/home/ec2-user/orion/kernell/vendor/phpmailer/phpmailer/src/PHPMailer.php:3817:     * such as a BLOB record from a database.
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/src/MimeType.php:696:        'odb' => 'application/vnd.oasis.opendocument.database',
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/src/MimeType.php:967:        'sqlite' => 'application/vnd.sqlite3',
/home/ec2-user/orion/kernell/vendor/guzzlehttp/psr7/src/MimeType.php:968:        'sqlite3' => 'application/vnd.sqlite3',
/home/ec2-user/orion/kernell/vendor/guzzlehttp/guzzle/src/Cookie/CookieJarInterface.php:14: * cookies from a file, database, etc.
/home/ec2-user/orion/kernell/vendor/composer/installed.json:1008:            "description": "Sends your logs to files, sockets, inboxes, databases and various web services",
/home/ec2-user/orion/kernell/vendor/composer/installed.json:3251:            "name": "vlucas/phpdotenv",
/home/ec2-user/orion/kernell/vendor/composer/installed.json:3256:                "url": "https://github.com/vlucas/phpdotenv.git",
/home/ec2-user/orion/kernell/vendor/composer/installed.json:3261:                "url": "https://api.github.com/repos/vlucas/phpdotenv/zipball/301c07936b16d88628b126b01d082ba153cf4c40",
/home/ec2-user/orion/kernell/vendor/composer/installed.json:3322:                "issues": "https://github.com/vlucas/phpdotenv/issues",
/home/ec2-user/orion/kernell/vendor/composer/installed.json:3323:                "source": "https://github.com/vlucas/phpdotenv/tree/v5.7.0"
/home/ec2-user/orion/kernell/vendor/composer/installed.json:3331:                    "url": "https://tidelift.com/funding/github/packagist/vlucas/phpdotenv",
/home/ec2-user/orion/kernell/vendor/composer/installed.json:3335:            "install-path": "../vlucas/phpdotenv"
/home/ec2-user/orion/kernell/vendor/composer/installed.php:457:        'vlucas/phpdotenv' => array(
/home/ec2-user/orion/kernell/vendor/composer/installed.php:462:            'install_path' => __DIR__ . '/../vlucas/phpdotenv',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:255:            0 => __DIR__ . '/..' . '/vlucas/phpdotenv/src',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:304:        'App\\Services\\Auth\\AuthDatabase' => __DIR__ . '/../..' . '/app/Services/Auth/AuthDatabase.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:435:        'Dotenv\\Dotenv' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Dotenv.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:436:        'Dotenv\\Exception\\ExceptionInterface' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Exception/ExceptionInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:437:        'Dotenv\\Exception\\InvalidEncodingException' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Exception/InvalidEncodingException.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:438:        'Dotenv\\Exception\\InvalidFileException' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Exception/InvalidFileException.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:439:        'Dotenv\\Exception\\InvalidPathException' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Exception/InvalidPathException.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:440:        'Dotenv\\Exception\\ValidationException' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Exception/ValidationException.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:441:        'Dotenv\\Loader\\Loader' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Loader/Loader.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:442:        'Dotenv\\Loader\\LoaderInterface' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Loader/LoaderInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:443:        'Dotenv\\Loader\\Resolver' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Loader/Resolver.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:444:        'Dotenv\\Parser\\Entry' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Parser/Entry.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:445:        'Dotenv\\Parser\\EntryParser' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Parser/EntryParser.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:446:        'Dotenv\\Parser\\Lexer' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Parser/Lexer.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:447:        'Dotenv\\Parser\\Lines' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Parser/Lines.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:448:        'Dotenv\\Parser\\Parser' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Parser/Parser.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:449:        'Dotenv\\Parser\\ParserInterface' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Parser/ParserInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:450:        'Dotenv\\Parser\\Value' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Parser/Value.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:451:        'Dotenv\\Repository\\AdapterRepository' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/AdapterRepository.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:452:        'Dotenv\\Repository\\Adapter\\AdapterInterface' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/AdapterInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:453:        'Dotenv\\Repository\\Adapter\\ApacheAdapter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/ApacheAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:454:        'Dotenv\\Repository\\Adapter\\ArrayAdapter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/ArrayAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:455:        'Dotenv\\Repository\\Adapter\\EnvConstAdapter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/EnvConstAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:456:        'Dotenv\\Repository\\Adapter\\GuardedWriter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/GuardedWriter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:457:        'Dotenv\\Repository\\Adapter\\ImmutableWriter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/ImmutableWriter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:458:        'Dotenv\\Repository\\Adapter\\MultiReader' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/MultiReader.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:459:        'Dotenv\\Repository\\Adapter\\MultiWriter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/MultiWriter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:460:        'Dotenv\\Repository\\Adapter\\PutenvAdapter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/PutenvAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:461:        'Dotenv\\Repository\\Adapter\\ReaderInterface' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/ReaderInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:462:        'Dotenv\\Repository\\Adapter\\ReplacingWriter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/ReplacingWriter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:463:        'Dotenv\\Repository\\Adapter\\ServerConstAdapter' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/ServerConstAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:464:        'Dotenv\\Repository\\Adapter\\WriterInterface' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/Adapter/WriterInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:465:        'Dotenv\\Repository\\RepositoryBuilder' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/RepositoryBuilder.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:466:        'Dotenv\\Repository\\RepositoryInterface' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Repository/RepositoryInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:467:        'Dotenv\\Store\\FileStore' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Store/FileStore.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:468:        'Dotenv\\Store\\File\\Paths' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Store/File/Paths.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:469:        'Dotenv\\Store\\File\\Reader' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Store/File/Reader.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:470:        'Dotenv\\Store\\StoreBuilder' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Store/StoreBuilder.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:471:        'Dotenv\\Store\\StoreInterface' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Store/StoreInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:472:        'Dotenv\\Store\\StringStore' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Store/StringStore.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:473:        'Dotenv\\Util\\Regex' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Util/Regex.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:474:        'Dotenv\\Util\\Str' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Util/Str.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:475:        'Dotenv\\Validator' => __DIR__ . '/..' . '/vlucas/phpdotenv/src/Validator.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:833:        'Orion\\Core\\Database\\Database' => __DIR__ . '/../..' . '/app/Core/Database/Database.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:834:        'Orion\\Core\\Database\\Model' => __DIR__ . '/../..' . '/app/Core/Database/Model.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:835:        'Orion\\Core\\Database\\QueryBuilder' => __DIR__ . '/../..' . '/app/Core/Database/QueryBuilder.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:836:        'Orion\\Core\\Database\\Repository' => __DIR__ . '/../..' . '/app/Core/Database/Repository.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:1116:        'SQLite3Exception' => __DIR__ . '/..' . '/symfony/polyfill-php83/Resources/stubs/SQLite3Exception.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:1132:        'Symfony\\Component\\Cache\\Adapter\\PdoAdapter' => __DIR__ . '/..' . '/symfony/cache/Adapter/PdoAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_static.php:1283:        'Symfony\\Component\\HttpFoundation\\Session\\Storage\\Handler\\PdoSessionHandler' => __DIR__ . '/..' . '/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_psr4.php:46:    'Dotenv\\' => array($vendorDir . '/vlucas/phpdotenv/src'),
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:37:    'App\\Services\\Auth\\AuthDatabase' => $baseDir . '/app/Services/Auth/AuthDatabase.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:168:    'Dotenv\\Dotenv' => $vendorDir . '/vlucas/phpdotenv/src/Dotenv.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:169:    'Dotenv\\Exception\\ExceptionInterface' => $vendorDir . '/vlucas/phpdotenv/src/Exception/ExceptionInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:170:    'Dotenv\\Exception\\InvalidEncodingException' => $vendorDir . '/vlucas/phpdotenv/src/Exception/InvalidEncodingException.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:171:    'Dotenv\\Exception\\InvalidFileException' => $vendorDir . '/vlucas/phpdotenv/src/Exception/InvalidFileException.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:172:    'Dotenv\\Exception\\InvalidPathException' => $vendorDir . '/vlucas/phpdotenv/src/Exception/InvalidPathException.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:173:    'Dotenv\\Exception\\ValidationException' => $vendorDir . '/vlucas/phpdotenv/src/Exception/ValidationException.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:174:    'Dotenv\\Loader\\Loader' => $vendorDir . '/vlucas/phpdotenv/src/Loader/Loader.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:175:    'Dotenv\\Loader\\LoaderInterface' => $vendorDir . '/vlucas/phpdotenv/src/Loader/LoaderInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:176:    'Dotenv\\Loader\\Resolver' => $vendorDir . '/vlucas/phpdotenv/src/Loader/Resolver.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:177:    'Dotenv\\Parser\\Entry' => $vendorDir . '/vlucas/phpdotenv/src/Parser/Entry.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:178:    'Dotenv\\Parser\\EntryParser' => $vendorDir . '/vlucas/phpdotenv/src/Parser/EntryParser.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:179:    'Dotenv\\Parser\\Lexer' => $vendorDir . '/vlucas/phpdotenv/src/Parser/Lexer.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:180:    'Dotenv\\Parser\\Lines' => $vendorDir . '/vlucas/phpdotenv/src/Parser/Lines.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:181:    'Dotenv\\Parser\\Parser' => $vendorDir . '/vlucas/phpdotenv/src/Parser/Parser.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:182:    'Dotenv\\Parser\\ParserInterface' => $vendorDir . '/vlucas/phpdotenv/src/Parser/ParserInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:183:    'Dotenv\\Parser\\Value' => $vendorDir . '/vlucas/phpdotenv/src/Parser/Value.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:184:    'Dotenv\\Repository\\AdapterRepository' => $vendorDir . '/vlucas/phpdotenv/src/Repository/AdapterRepository.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:185:    'Dotenv\\Repository\\Adapter\\AdapterInterface' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/AdapterInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:186:    'Dotenv\\Repository\\Adapter\\ApacheAdapter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/ApacheAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:187:    'Dotenv\\Repository\\Adapter\\ArrayAdapter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/ArrayAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:188:    'Dotenv\\Repository\\Adapter\\EnvConstAdapter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/EnvConstAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:189:    'Dotenv\\Repository\\Adapter\\GuardedWriter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/GuardedWriter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:190:    'Dotenv\\Repository\\Adapter\\ImmutableWriter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/ImmutableWriter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:191:    'Dotenv\\Repository\\Adapter\\MultiReader' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/MultiReader.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:192:    'Dotenv\\Repository\\Adapter\\MultiWriter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/MultiWriter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:193:    'Dotenv\\Repository\\Adapter\\PutenvAdapter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/PutenvAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:194:    'Dotenv\\Repository\\Adapter\\ReaderInterface' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/ReaderInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:195:    'Dotenv\\Repository\\Adapter\\ReplacingWriter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/ReplacingWriter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:196:    'Dotenv\\Repository\\Adapter\\ServerConstAdapter' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/ServerConstAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:197:    'Dotenv\\Repository\\Adapter\\WriterInterface' => $vendorDir . '/vlucas/phpdotenv/src/Repository/Adapter/WriterInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:198:    'Dotenv\\Repository\\RepositoryBuilder' => $vendorDir . '/vlucas/phpdotenv/src/Repository/RepositoryBuilder.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:199:    'Dotenv\\Repository\\RepositoryInterface' => $vendorDir . '/vlucas/phpdotenv/src/Repository/RepositoryInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:200:    'Dotenv\\Store\\FileStore' => $vendorDir . '/vlucas/phpdotenv/src/Store/FileStore.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:201:    'Dotenv\\Store\\File\\Paths' => $vendorDir . '/vlucas/phpdotenv/src/Store/File/Paths.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:202:    'Dotenv\\Store\\File\\Reader' => $vendorDir . '/vlucas/phpdotenv/src/Store/File/Reader.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:203:    'Dotenv\\Store\\StoreBuilder' => $vendorDir . '/vlucas/phpdotenv/src/Store/StoreBuilder.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:204:    'Dotenv\\Store\\StoreInterface' => $vendorDir . '/vlucas/phpdotenv/src/Store/StoreInterface.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:205:    'Dotenv\\Store\\StringStore' => $vendorDir . '/vlucas/phpdotenv/src/Store/StringStore.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:206:    'Dotenv\\Util\\Regex' => $vendorDir . '/vlucas/phpdotenv/src/Util/Regex.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:207:    'Dotenv\\Util\\Str' => $vendorDir . '/vlucas/phpdotenv/src/Util/Str.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:208:    'Dotenv\\Validator' => $vendorDir . '/vlucas/phpdotenv/src/Validator.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:566:    'Orion\\Core\\Database\\Database' => $baseDir . '/app/Core/Database/Database.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:567:    'Orion\\Core\\Database\\Model' => $baseDir . '/app/Core/Database/Model.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:568:    'Orion\\Core\\Database\\QueryBuilder' => $baseDir . '/app/Core/Database/QueryBuilder.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:569:    'Orion\\Core\\Database\\Repository' => $baseDir . '/app/Core/Database/Repository.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:849:    'SQLite3Exception' => $vendorDir . '/symfony/polyfill-php83/Resources/stubs/SQLite3Exception.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:865:    'Symfony\\Component\\Cache\\Adapter\\PdoAdapter' => $vendorDir . '/symfony/cache/Adapter/PdoAdapter.php',
/home/ec2-user/orion/kernell/vendor/composer/autoload_classmap.php:1016:    'Symfony\\Component\\HttpFoundation\\Session\\Storage\\Handler\\PdoSessionHandler' => $vendorDir . '/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php',
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Type/Integer.php:45:     * @phpstan-ignore property.readOnlyByPhpDocDefaultValue
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Type/Integer.php:152:            /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Uuid.php:233:     * @phpstan-ignore property.readOnlyByPhpDocDefaultValue
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Uuid.php:240:     * @phpstan-ignore property.readOnlyByPhpDocDefaultValue
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Uuid.php:329:        /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Uuid.php:332:        /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Uuid.php:335:        /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Uuid.php:338:        /** @phpstan-ignore property.readOnlyByPhpDocAssignNotInConstructor */
/home/ec2-user/orion/kernell/vendor/ramsey/uuid/src/Codec/OrderedTimeCodec.php:30: * closer to sequential when storing the bytes. According to Percona, this optimization can improve database INSERT and
/home/ec2-user/orion/kernell/vendor/league/mime-type-detection/src/GeneratedExtensionToMimeTypeMap.php:704:        'odb' => 'application/vnd.oasis.opendocument.database',
/home/ec2-user/orion/kernell/vendor/league/mime-type-detection/src/GeneratedExtensionToMimeTypeMap.php:974:        'sqlite' => 'application/vnd.sqlite3',
/home/ec2-user/orion/kernell/vendor/league/mime-type-detection/src/GeneratedExtensionToMimeTypeMap.php:975:        'sqlite3' => 'application/vnd.sqlite3',
/home/ec2-user/orion/kernell/vendor/league/mime-type-detection/src/GeneratedExtensionToMimeTypeMap.php:1727:        'application/vnd.oasis.opendocument.database' => ['odb'],
/home/ec2-user/orion/kernell/vendor/league/mime-type-detection/src/GeneratedExtensionToMimeTypeMap.php:1801:        'application/vnd.sqlite3' => ['sqlite', 'sqlite3'],
/home/ec2-user/orion/kernell/app/Services/Admin/SystemHealthService.php:13:            'database'=>true,
/home/ec2-user/orion/kernell/app/Services/Auth/AuthDatabase.php:4:use PDO; use PDOException;
/home/ec2-user/orion/kernell/app/Services/Auth/AuthDatabase.php:5:final class AuthDatabase {
/home/ec2-user/orion/kernell/app/Services/Auth/AuthDatabase.php:6: private ?PDO $connection=null;
/home/ec2-user/orion/kernell/app/Services/Auth/AuthDatabase.php:7: public function connection():PDO{
/home/ec2-user/orion/kernell/app/Services/Auth/AuthDatabase.php:8:  if($this->connection instanceof PDO)return $this->connection;
/home/ec2-user/orion/kernell/app/Services/Auth/AuthDatabase.php:11:  try{$this->connection=new PDO($dsn,(string)env('ORION_AUTH_DB_USERNAME',''),(string)env('ORION_AUTH_DB_PASSWORD',''),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}
/home/ec2-user/orion/kernell/app/Services/Auth/AuthDatabase.php:12:  catch(PDOException $e){throw new \RuntimeException('No fue posible conectar con la base de datos de Auth.',0,$e);}
/home/ec2-user/orion/kernell/app/Services/Auth/LoginServide.php:14:        private readonly AuthDatabase $database
/home/ec2-user/orion/kernell/app/Services/Auth/LoginServide.php:22:        $statement=$this->database->connection()->prepare(
/home/ec2-user/orion/kernell/app/Services/Auth/LoginServide.php:48:            $u=$this->database->connection()->prepare('UPDATE dte_usuarios SET ultimo_login=CURRENT_TIMESTAMP,intentos_fallidos=0 WHERE id=:id');
/home/ec2-user/orion/kernell/app/Services/Auth/RefreshTokenService.php:7: public function __construct(private readonly AuthDatabase $database){}
/home/ec2-user/orion/kernell/app/Services/Auth/RefreshTokenService.php:11:  $s=$this->database->connection()->prepare('INSERT INTO orion_token (type,token,timestamp,life,owner,user_id) VALUES (:type,:token,CURRENT_TIMESTAMP,:life,:owner,:user_id)');
/home/ec2-user/orion/kernell/app/Services/Auth/RefreshTokenService.php:16:  $s=$this->database->connection()->prepare('SELECT t.codid,t.user_id,t.timestamp,t.life,u.id,u.email,u.nombres,u.apellidos,u.usuario,u.rol,u.es_administrador,u.permisos,u.avatar_url,u.password_hash,u.activo,u.empresa_nit,e.uuid AS empresa_uuid,e.nit AS empresa_nit_db,e.estado AS empresa_estado,e.nombre AS empresa_nombre,e.razon_social AS empresa_razon_social FROM orion_token t INNER JOIN dte_usuarios u ON u.id=t.user_id INNER JOIN empresas e ON e.nit=u.empresa_nit WHERE t.type=:type AND t.token=:token AND t.life>0 AND t.timestamp + make_interval(secs => t.life)>CURRENT_TIMESTAMP AND u.activo=TRUE AND e.estado=:empresa_estado LIMIT 1');
/home/ec2-user/orion/kernell/app/Services/Auth/RefreshTokenService.php:23: public function revoke(string $refreshToken):void{if(trim($refreshToken)==='')return;$s=$this->database->connection()->prepare('UPDATE orion_token SET life=0 WHERE type=:type AND token=:token');$s->execute(['type'=>self::TOKEN_TYPE,'token'=>hash('sha256',$refreshToken)]);}
/home/ec2-user/orion/kernell/app/Services/Auth/RegisterService.php:11:    public function __construct(private readonly AuthDatabase $database) {}
/home/ec2-user/orion/kernell/app/Services/Auth/RegisterService.php:32:        $db=$this->database->connection();
/home/ec2-user/orion/kernell/app/Services/DTE/DteTenantConfiguration.php:6:use PDO;
/home/ec2-user/orion/kernell/app/Services/DTE/DteTenantConfiguration.php:13:        private readonly PDO $pdo,
/home/ec2-user/orion/kernell/app/Services/DTE/DteTenantConfiguration.php:23:        $stmt = $this->pdo->prepare(
/home/ec2-user/orion/kernell/app/Services/DTE/DteTenantConfigurationWriter.php:6:use PDO;
/home/ec2-user/orion/kernell/app/Services/DTE/DteTenantConfigurationWriter.php:13:        private readonly PDO $pdo,
/home/ec2-user/orion/kernell/app/Services/DTE/DteTenantConfigurationWriter.php:92:        $stmt = $this->pdo->prepare($sql);
/home/ec2-user/orion/kernell/app/Services/DTE/DteTenantConfigurationWriter.php:112:        $lookup = $this->pdo->prepare(
/home/ec2-user/orion/kernell/app/Services/DTE/SchemaEngine.php:7:use Orion\Core\Database\Database;
/home/ec2-user/orion/kernell/app/Services/DTE/SchemaEngine.php:12:    public function __construct(private Database $database)
/home/ec2-user/orion/kernell/app/Services/DTE/SchemaEngine.php:18:        $pdo = $this->database->connection();
/home/ec2-user/orion/kernell/app/Services/DTE/SchemaEngine.php:24:            $stmt = $pdo->prepare($sql);
/home/ec2-user/orion/kernell/app/Services/DTE/SchemaEngine.php:30:            $stmt = $pdo->prepare($sql);
/home/ec2-user/orion/kernell/app/Services/Queue/QueueDispatcher.php:7:use App\Core\Database\Database;
/home/ec2-user/orion/kernell/app/Services/Queue/QueueDispatcher.php:13:    public function __construct(private readonly Database $database)
/home/ec2-user/orion/kernell/app/Services/Queue/QueueDispatcher.php:18:     * Transitional database-backed dispatcher.
/home/ec2-user/orion/kernell/app/Services/Queue/QueueDispatcher.php:31:        $statement = $this->database->connection()->prepare(
/home/ec2-user/orion/kernell/app/Services/Queue/QueueWorker.php:7:use App\Core\Database\Database;
/home/ec2-user/orion/kernell/app/Services/Queue/QueueWorker.php:11:    public function __construct(private readonly Database $database)
/home/ec2-user/orion/kernell/app/Services/Queue/QueueWorker.php:16:     * Transitional database-backed worker.
/home/ec2-user/orion/kernell/app/Services/Queue/QueueWorker.php:21:        $statement = $this->database->connection()->query(
/home/ec2-user/orion/kernell/app/Services/Queue/QueueWorker.php:34:        $statement = $this->database->connection()->prepare(
/home/ec2-user/orion/kernell/app/Controllers/Api/InvoiceController.php:12:use Orion\Core\Database\Database;
/home/ec2-user/orion/kernell/app/Controllers/Api/InvoiceController.php:37:            $database = new Database();
/home/ec2-user/orion/kernell/app/Controllers/Api/InvoiceController.php:39:                new \App\Services\DTE\SchemaEngine($database),
/home/ec2-user/orion/kernell/app/Controllers/Api/InvoiceController.php:41:                new DteTenantConfiguration($database->connection(), new SecretCipher()),
/home/ec2-user/orion/kernell/app/Controllers/Api/InvoiceController.php:78:            $database = new Database();
/home/ec2-user/orion/kernell/app/Controllers/Api/InvoiceController.php:79:            $config = (new DteTenantConfiguration($database->connection(), new SecretCipher()))->resolve(
/home/ec2-user/orion/kernell/app/Controllers/Api/InvoiceController.php:115:            $schema = (new \App\Services\DTE\SchemaEngine(new Database()))->load($type);
/home/ec2-user/orion/kernell/app/Controllers/Api/AuthController.php:9:use App\Services\Auth\AuthDatabase;
/home/ec2-user/orion/kernell/app/Controllers/Api/AuthController.php:21:        $db=new AuthDatabase();
/home/ec2-user/orion/kernell/app/Controllers/Api/AuthController.php:67:            $db=new AuthDatabase();
/home/ec2-user/orion/kernell/app/Core/Loadder.php:21:use Orion\Core\Database\Database;
/home/ec2-user/orion/kernell/app/Core/Loadder.php:34:            Database::class,
/home/ec2-user/orion/kernell/app/Core/Loadder.php:35:            new Database()
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:5: * Archivo       : Database.php
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:6: * Ruta          : /app/Core/Database/Database.php
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:7: * Función       : Administrador central de conexiones PDO.
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:21:namespace Orion\Core\Database;
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:23:use PDO;
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:24:use PDOException;
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:26:final class Database
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:28:    private ?PDO $connection = null;
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:30:    public function connection(): PDO
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:32:        if ($this->connection instanceof PDO) {
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:37:            "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:38:            env('DB_HOST'),
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:40:            env('DB_DATABASE')
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:45:            $this->connection = new PDO(
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:47:                env('DB_USERNAME'),
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:50:                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:51:                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:52:                    PDO::ATTR_EMULATE_PREPARES => false,
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:56:        } catch (PDOException $e) {
/home/ec2-user/orion/kernell/app/Core/Database/Database.php:58:            throw new PDOException(
/home/ec2-user/orion/kernell/app/Core/Database/Model.php:6: * Ruta          : /app/Core/Database/Model.php
/home/ec2-user/orion/kernell/app/Core/Database/Model.php:9: *      - Database.php
/home/ec2-user/orion/kernell/app/Core/Database/Model.php:19:namespace Orion\Core\Database;
/home/ec2-user/orion/kernell/app/Core/Database/QueryBuilder.php:4:namespace Orion\Core\Database;
/home/ec2-user/orion/kernell/app/Core/Database/QueryBuilder.php:6:use PDO;
/home/ec2-user/orion/kernell/app/Core/Database/QueryBuilder.php:10:    private PDO $db;
/home/ec2-user/orion/kernell/app/Core/Database/QueryBuilder.php:21:    public function __construct(PDO $pdo)

===== ORION ENV =====
/etc/kena/kena.env
/etc/orion/orion.env
/home/ec2-user/orion/backup-20260919-173352/kernell/.env
/home/ec2-user/orion/backup-20260919-174130/kernell/.env
/home/ec2-user/orion/backup-20260920-044205/kernell/.env
/home/ec2-user/orion/backup-20260920-061745/kernell/.env
/home/ec2-user/orion/backup-20260920-062901/kernell/.env
/home/ec2-user/orion/backup-20260920-063007/kernell/.env
/home/ec2-user/orion/backup-20260920-063249/kernell/.env
/home/ec2-user/orion/backup-20260921-051230/frontend/app/config.php
/home/ec2-user/orion/backup-20260921-051230/kernell/.env
/home/ec2-user/orion/backup-20260921-052037/frontend/app/config.php
/home/ec2-user/orion/backup-20260921-052037/kernell/.env
/home/ec2-user/orion/kernell/.env

===== SERVICIOS ORION =====

===== PROCESOS ORION =====
root     3202558  0.0  0.7 424980 14748 ?        Ss   Sep19   0:18 php-fpm: master process (/etc/php-fpm.conf)
apache   3202559  0.0  1.0 425300 20336 ?        S    Sep19   0:00 php-fpm: pool www
apache   3202560  0.0  0.9 424980 17472 ?        S    Sep19   0:00 php-fpm: pool www
apache   3202561  0.0  1.0 425248 20112 ?        S    Sep19   0:00 php-fpm: pool www
apache   3202562  0.0  1.3 502512 25132 ?        S    Sep19   0:00 php-fpm: pool www
apache   3202563  0.0  1.0 425296 19112 ?        S    Sep19   0:00 php-fpm: pool www
apache   3317581  0.0  0.9 425152 17824 ?        S    Sep21   0:00 php-fpm: pool www
[ec2-user@ip-172-31-1-105 ~]$
