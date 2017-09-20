<?php
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Module
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
    
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Module
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
namespace ImhPropel;

use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface as Autoloader;
use Zend\ModuleManager\Feature\BootstrapListenerInterface as BootstrapListener;
use Zend\ModuleManager\Feature\ConfigProviderInterface as Config;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface as ConsoleUsage;
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Module implements ConsoleUsage, Config, Autoloader, BootstrapListener
{
    public function onBootstrap(EventInterface $e)
    {
        $this->propelInit($e->getApplication());
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    /**
     * @param AdapterInterface $console
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            array(
                'php index.php propel build [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Build the model classes based on Propel XML schemas'
            ),
            array(
                'php index.php propel build-sql [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Build SQL files'
            ),
            array(
                'php index.php propel diff [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Generate diff classes'
            ),
            array(
                'php index.php propel migration-data [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Generate data migration file'
            ),
            array(
                'php index.php propel migrate-data [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Generate data migration file'
            ),
            array(
                'php index.php propel down [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Execute migrations down'
            ),
            array(
                'php index.php propel migrate [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Execute all pending migrations'
            ),
            array(
                'php index.php propel status [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Get migration status'
            ),
            array(
                'php index.php propel up [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Execute migrations up'
            ),
            array(
                'php index.php propel migration-diff [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Generate diff classes'
            ),
            array(
                'php index.php propel migration-down [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Execute migrations down'
            ),
            array(
                'php index.php propel migration-migrate [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Execute all pending migrations'
            ),
            array(
                'php index.php propel migration-status [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Get migration status'
            ),
            array(
                'php index.php propel migration-up [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Execute migrations up'
            ),
            array(
                'php index.php propel model-build [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Build the model classes based on Propel XML schemas'
            ),
            array(
                'php index.php propel sql-build [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Build SQL files'
            ),
            array(
                'php index.php propel update [all|NAMESPACE|[comma delimited NAMESPACES]',
                'Execute pending migrations, build SQL files, build Propel classes'
            ),
        );
    }
    
    /**
     * Setup Propel connection for all loaded modules
     */
    public function propelInit($app)
    {
        $serviceContainer = Propel::getServiceContainer();
        $config = $app->getServiceManager()->get('config');
        $config = $config['propel']['database']['connections'];
        $default_settings = ($config['default']) ? $config['default'] : array();
        $db_settings = array();
        foreach ($config as $key => $settings) {
             $settings = array_merge($default_settings,$settings);
             $settings['adapter']              = (@$settings['adapter']) ?: 'mysql';
             $settings['host']                 = (@$settings['host'])    ?: 'localhost';
             $settings['dbname']               = (@$settings['dbname'])  ?: $key;
             $dsn = null;
             if (isset($settings['dsn'])) {
                 $dsn = $settings['dsn'];
             } elseif (isset($settings['dbname'])) {
                 switch ($settings['adapter']) {
                    case 'mysql':
                        $dsn = sprintf(
                            "mysql:host=%s;dbname=%s",
                            $settings['host'],
                            $settings['dbname']
                        );
                        break;
                    case 'oci':
                        $dsn = sprintf(
                            "oci:dbname=//%s/%s",
                            $settings['host'],
                            $settings['dbname']
                        );
                        break;
                    case 'pgsql':
                        $dsn = sprintf(
                            "pgsql:host=%s;port=5432;dbname=%s;user=%s;password=%s",
                            $settings['host'],
                            $settings['dbname'],
                            $settings['user'],
                            $settings['password']
                        );
                        break;
                    case 'sqlite':
                        $dsn = sprintf(
                            "sqlite:%s/%s",
                            $settings['host'],
                            $settings['dbname']
                        );
                        break;
                 }
             }
             if ($dsn) {
                $serviceContainer->setAdapterClass($key, $settings['adapter']);
                $manager = new ConnectionManagerSingle();
                $manager->setConfiguration(
                    array (
                        'dsn'      => $dsn,
                        'user'     => $settings['user'],
                        'password' => $settings['password'],
                    )
                );
                $serviceContainer->setConnectionManager($key, $manager); 
                $loggingEnabled = (@$settings['logging_enabled']) ?: false;
                if ($loggingEnabled) {
                    $logDir     = (array_key_exists('log_dir', $settings)) ? $settings['log_dir'] : 'data/logs';
                    $logfile    = (@$settings['log_file']) ?: $key . '.log';
                    if ($logDir) {
                        $logfile = realpath($logDir) . '/' . $logfile;
                    }
                    $logger     = new Logger($key);
                    $logger->pushHandler(new StreamHandler($logfile, Logger::DEBUG));
                    $serviceContainer->setLogger($key, $logger);
                    $con = $serviceContainer->getConnectionManager($key)->getWriteConnection($serviceContainer->getAdapter($key));
                    $con->useDebug(true);
                }
            }
        }
    }
}
