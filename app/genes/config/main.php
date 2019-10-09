<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

$config = [
    'id' => 'genes',
    'name' => 'Open Longevity Genes',
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-GB', // todo костыль на то, что у нас переводы не в yii-формате ['english phrase' => 'русская фраза'], переделаем?
    'basePath' => dirname(__DIR__),
    'homeUrl' => '/',
    'controllerNamespace' => 'genes\controllers',
    'vendorPath' => '@common/vendor',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-genes',
            'cookieValidationKey' => '123',
        ],
        'db' => [
            'charset' => 'utf8',
            'class' => yii\db\Connection::class,
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-genes', 'httpOnly' => true],
        ],
        'i18n' => [
            'translations' => [
                'main' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => __DIR__ . '/../assets/translations',
//                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'main' => 'main.php',
                    ],
                ],
            ],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'genes',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../runtime/assets',
            'baseUrl' => '/runtime/assets',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'about' => 'site/about'
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            \genes\application\service\GeneInfoServiceInterface::class => \genes\application\service\GeneInfoService::class,
            \genes\infrastructure\repository\GeneRepositoryInterface::class => \genes\infrastructure\repository\GeneRepository::class
        ]
    ],
    'defaultRoute' => 'site/index',
    'params' => $params,
    'runtimePath' => __DIR__ . '/../runtime',
    'on beforeAction' => function ($event) { // todo привести язык на фронте к стандарту ln-LN
        $language = $_GET['lang'] ?? $_COOKIE['lang'] ?? Yii::$app->language;
        if($language == 'EN') {
            $language = 'en-US';
        }
        if($language == 'RU') {
            $language = 'ru-RU';
        }
        if(Yii::$app->language != $language) {
            Yii::$app->language = $language;
        }
        if($_COOKIE['lang'] != $language) {
            setcookie('lang', $language, $expire = 0, $path = "/");
        }
    },
];


if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;