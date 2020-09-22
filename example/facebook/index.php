<?php

require __DIR__.'/../../vendor/autoload.php';

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO\MySQL\MySQLCredentialsProvider;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookProcedureOptions;

define('APP_ID', '');
define('APP_SECRET', '');

$dsn = 'mysql:host=localhost;dbname=ldl_auth';
$pdo = new \PDO($dsn,'root', '',[
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
]);

$fbProcedure = new FacebookProcedure(
    new MySQLCredentialsProvider(
        $pdo,
        'user',
        'password'
    ),
    new FacebookProcedureOptions(
        APP_ID,
        APP_SECRET,
        'http://localhost:8080/login/v1.0/login'
    ),
    null
);

?>

<html>
    <body>
        <a href="<?php echo $fbProcedure->getAuthorizationEndpoint(); ?>">Login Facebook</a>
    </body>
</html>
