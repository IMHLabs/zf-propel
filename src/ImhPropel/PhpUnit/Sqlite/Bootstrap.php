<?php
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/PhpUnit/Bootstrap
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
    
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/PhpUnit/Bootstrap
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
namespace ImhPropel\PhpUnit\Sqlite;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

class Bootstrap
{
    /**
     * @var object
     */
    protected static $serviceManager;
    
    /**
     * @var array
     */
    protected static $config;

    /**
     * @var string
     */
    protected static $connection;
    
    /**
     * @var string
     */
    protected static $path;
    
    /**
     * @var string
     */
    protected static $module_name;

    /**
     * Set Service Manager
     * 
     * @param object $serviceManager
     * @return void
     */
    public static function setServiceManager($serviceManager)
    {
        static::$serviceManager = $serviceManager;
    }

    /**
     * Get Service Manager
     * 
     * @return object
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    /**
     * Set Config
     *
     * @param string $config
     * @return void
     */
    public static function setConfig($config)
    {
        static::$config = $config;
    }

    /**
     * Get Config
     * 
     * @return string
     */
    public static function getConfig()
    {
        return static::$config;
    }
    
    /**
     * Set Connection Name
     * 
     * @param string $connection
     * @return void
     */
    public static function setConnection($connection)
    {
        static::$connection = $connection;
    }
    
    /**
     * Get Connection Name
     * 
     * @return string
     */
    public static function getConnection()
    {
        return static::$connection;
    }

    /**
     * Set Module Test Data Directory
     * 
     * @param string $path
     * @return void
     */
    public static function setPath($path)
    {
        static::$path = $path;
    }
    
    /**
     * Get Module Test Data Directory
     * 
     * @return string
     */
    public static function getPath()
    {
        return static::$path;
    }
    
    /**
     * Set Module Name
     * 
     * @param string $module
     * @return void
     */
    public static function setModule($module)
    {
        static::$module_name = $module;
    }
    
    /**
     * Get Module Name
     * 
     * @return string
     */
    public static function getModule()
    {
        return static::$module_name;
    }

    /**
     * Initialize Test Propel Connections for Sqlite
     */
    public static function initPropel()
    {
        $serviceContainer   = Propel::getServiceContainer();
        $conectionName      = static::getConnection();
        $config             = static::getConfig();
        $propelConfig       = $config['propel'];
        $dsn                = $propelConfig['database']['connections'][$conectionName]['dsn'];
        $serviceContainer->setAdapterClass($conectionName, 'sqlite');
        $manager = new ConnectionManagerSingle();
        $manager->setConfiguration(
            array (
                'dsn'      => $dsn,
            )
        );
        $serviceContainer->setConnectionManager($conectionName, $manager);
    }
    
    /**
     * Initialize Test Sqlite Database
     */
    public static function initDatabase()
    {
        static::_deleteDatabase();
        static::_createConnection();
        static::_createMigrations();
        static::_applyMigrations();
   }
   
    /**
     * Delete Test database to ensure all tests are run clean
     */
   protected static function _deleteDatabase()
    {
        $config             = static::getConfig();
        $conectionName      = static::getConnection();
        $propelConfig       = $config['propel'];
        $dsn                = $propelConfig['database']['connections'][$conectionName]['dsn'];
        //Destroy old test database
        $dbfile = preg_replace('/^sqlite:/','',$dsn);
        if (realpath($dbfile)) {
            unlink(realpath($dbfile));
        }
    }
        
    /**
     * Create connection to test database
     */
    protected static function _createConnection()
    {
        $config_path        = static::getPath() . '/propel.json';
        $config             = static::getConfig();
        $connectionName     = static::getConnection();
        $propelConfig       = $config['propel'];
        // Create database connection
        $propelConfig['database']['connections'][$connectionName]['adapter'] = 'sqlite';
        $propelConfig['database']['connections'][$connectionName]['user'] = '';
        $propelConfig['database']['connections'][$connectionName]['password'] = '';
        file_put_contents ($config_path, json_encode(array('propel' => $propelConfig)));     
    }
    
    /**
     * Delete migration(s) generated during previous test execution
     */
    protected static function _deleteMigrations()
    {
        $migrationDir = static::getPath() . '/migrations';
        //Destroy old test migrations
        if (realpath($migrationDir)) {
            if ($handle = opendir(realpath($migrationDir))) {
                while (false !== ($entry = readdir($handle))) {
                    if (preg_match('/^PropelMigration/',$entry)) {
                        $filepath = realpath($migrationDir . '/' . $entry);
                        unlink($filepath);
                    }
                }
                closedir($handle);
            }
        }
    }
    
    /**
     * Create migration(s) to reflect most recent version of database
     */
    protected static function _createMigrations()
    {
        static::_deleteMigrations();
        $modulemanager          = static::getServiceManager()->get('ModuleManager');
        $moduleObj              = $modulemanager->loadModule(static::getModule());
        $module_config             = $moduleObj->getConfig();
        $module_schema_path     = realpath($module_config['propel']['paths']['schema']);
        $propel_command            = sprintf(
            '%s/bin/propel migration:diff --config-dir="%s" --schema-dir="%s" --output-dir="%s"',
            static::findParentPath('vendor'),
            static::getPath(),
            $module_schema_path,
            static::getPath() . '/migrations');
        $results = exec($propel_command,$output);
    }

    /**
     * Apply migrations to empty database
     */
    protected static function _applyMigrations()
    {
        $propel_command            = sprintf(
            '%s/bin/propel migrate --config-dir="%s" --output-dir="%s"',
            static::findParentPath('vendor'),
            static::getPath(),
            static::getPath() . '/migrations');
        $results = exec($propel_command,$output);
   }

   /**
    * Find parent path
    * 
    * @param unknown $path
    * @return boolean|string
    */
    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}

