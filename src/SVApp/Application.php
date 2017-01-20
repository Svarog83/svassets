<?php

namespace SVApp;

use SVApp\Classes\MyRedisProvider;
use SVApp\Classes\RedisCache;
use SVApp\Controllers\AssetsControllerProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Cache\ApcuCache;
use Silex\Application as SilexApplication;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Sorien\Provider\PimpleDumpProvider;
use SVApp\Controllers\JSONControllerProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\Loader\YamlFileLoader;

class Application extends SilexApplication
{
    private $rootDir;
    private $env;

	/**
	 * Application constructor.
	 *
	 * @param array $env
	 */
	public function __construct($env)
    {
        $this->rootDir = __DIR__.'/../../';
        $this->env = $env;

        parent::__construct();

        $app = $this;


		//My settings
		$app['default_timezone'] = 'UTC';
		$app['orm.default_timezone'] = 'UTC';
		date_default_timezone_set('UTC');

		$app['redis.serializer.igbinary'] = false;
		$app['redis.timeout'] = 60*60;
		$app['redis.prefix'] = 'AssetsSV:';

		/*$app->register(new ClientServiceProvider(), [
			'predis.parameters' => 'tcp://127.0.0.1:6379',
			'predis.options'    => [
				'prefix'  => 'assetsSV:',
				'profile' => '3.0',
			],
		]);*/

		$app->register(new MyRedisProvider(), [
			'redis.host' => '127.0.0.1',
			'redis.port' => 6379,
			'redis.prefix' => $app['redis.prefix'],
			'redis.database' => '0'
		]);

		$app['cache'] = function () use ($app) {
			$cacheDriver = new RedisCache();
			$cacheDriver->setRedis($app['redis']);

			return $cacheDriver;
		};

		$app['apcu_cache'] = function () use ($app) {
			$cacheDriver = new ApcuCache();

			return $cacheDriver;
		};

		// Override these values in resources/config/prod.php file
        $app['debug'] = false;
		$app['var_dir'] = $this->rootDir.'/var';
        $app['locale'] = 'ru';
        $app['http_cache.cache_dir'] = function (Application $app) {
            return $app['var_dir'].'/cache/http';
        };
        $app['monolog.options'] = [
            'monolog.logfile' => $app['var_dir'].'/logs/app.log',
            'monolog.name' => 'app',
            'monolog.level' => 300, // = Logger::WARNING
        ];
        $app['security.users'] = array('alice' => array('ROLE_USER', 'dtnjrhtc'));

        $configFile = sprintf('%s/resources/config/%s.php', $this->rootDir, $env);
        if (!file_exists($configFile)) {
            throw new \RuntimeException(sprintf('The file "%s" does not exist.', $configFile));
        }
        require $configFile;

        $app->register(new DoctrineServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new HttpCacheServiceProvider());
        $app->register(new HttpFragmentServiceProvider());
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new ValidatorServiceProvider());
		$app->register(new PimpleDumpProvider()); //enable it to refresh the pimple.json file (to refresh call _dump)
		$app->register(new DoctrineOrmServiceProvider());

		$app['session.storage.handler'] = function ($app) {
			$sessionTimeout = 60 * 60 * 24 * 7; // 1 week
			$sessionOptions = [ 'key_prefix' => 'ses:'];
			return new RedisSessionHandler($app['redis'], $sessionTimeout, $sessionOptions);
		};

		$app['orm.em.config']->setQueryCacheImpl( $app['cache'] );
		$app['orm.em.config']->setResultCacheImpl( $app['cache'] );
		$app['orm.em.config']->setMetadataCacheImpl( $app['cache'] );

        $app->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'admin' => array(
                    'pattern' => '^/',
                    'form' => array(
                        'login_path' => '/login',
                    ),
                    'logout' => true,
                    'anonymous' => true,
                    'users' => $app['security.users'],
                ),
            ),
        ));
        $app['security.default_encoder'] = function () {
            return new PlaintextPasswordEncoder();
        };
        $app['security.utils'] = function ($app) {
            return new AuthenticationUtils($app['request_stack']);
        };

        $app->register(new TranslationServiceProvider());
        $app['translator'] = $app->extend(
			'translator', function ($translator) {
            $translator->addLoader('yaml', new YamlFileLoader());
			$translator->addResource('yaml', $this->rootDir.'/resources/translations/en.yml', 'en');
			$translator->addResource('yaml', $this->rootDir.'/resources/translations/ru.yml', 'ru');

            return $translator;
        });

        $app->register(new MonologServiceProvider(), $app['monolog.options']);

        $app->register(new TwigServiceProvider(), array(
            'twig.options' => array(
                'cache' => $app['debug'] ? false : $app['var_dir'].'/cache/twig',
				'auto_reload' => true,
                'strict_variables' => true,
            ),
            'twig.form.templates' => array('bootstrap_3_horizontal_layout.html.twig'),
            'twig.path' => array($this->rootDir.'/resources/templates', $this->rootDir.'/src/SVApp/Views'),
        ));

        $app['twig'] = $app->extend('twig', function ($twig, $app) {
            $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
                $base = $app['request_stack']->getCurrentRequest()->getBasePath();

                return sprintf($base.'/'.$asset, ltrim($asset, '/'));
            }));

            return $twig;
        });

        if ($app['debug']) {
            $app->register(new WebProfilerServiceProvider(), array(
                'profiler.cache_dir' => $app['var_dir'].'/cache/profiler',
                'profiler.mount_prefix' => '/_profiler', // this is the default
            ));
        }

        $app->mount('', new ControllerProvider());
		$app->mount('/assets/', new AssetsControllerProvider());
		$app->mount('/json/', new JSONControllerProvider());
    }

    public function getRootDir()
    {
        return $this->rootDir;
    }

    public function getEnv()
    {
        return $this->env;
    }
}
