<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

$parametersYamlFile = __DIR__ . '/config/parameters.yml';
$parametersYamlDistFile = $parametersYamlFile . '.j2';

try {
    echo "Try to parse $parametersYamlDistFile file ...";
    Yaml::parseFile($parametersYamlDistFile);
    echo " ok\n";

    echo "Try to parse $parametersYamlFile file ...";
    $config = Yaml::parseFile($parametersYamlFile);
    echo " ok\n";

    $params = $config['parameters'];
    $host = $params['redis.host'];
    $port = $params['redis.port'];

    echo "Try to connect Redis $host:$port ...";
    (new Redis())->connect($host, $port);
    echo " ok\n";

    echo "Checking databases connections ...\n";
    $connections = [];
    foreach ($params as $key => $value) {
        $keyData = explode('.', $key);
        if ($keyData[0] === 'db') {
            list(, $connection, $param) = $keyData;
            $connections[$connection][$param] = $value;
        }
    }

    foreach ($connections as $connectionData) {
        $dsn = sprintf('mysql:host=%s:%d;dbname=%s', $connectionData['host'], $connectionData['port'], $connectionData['name']);
        echo $dsn;
        new PDO($dsn, $connectionData['user'], $connectionData['password']);
        echo " ok\n";
    }

    echo "Success!\n";

    exit(0);
} catch (ParseException $e) {
    echo "\ncan't parse yaml file\n";
} catch (RedisException $e) {
    echo "\ncan't connect Redis\n";
} catch (PDOException $e) {
    echo "\ncan't connect Database\n";
} finally {
    echo $e->getMessage();
    exit(1);
}

