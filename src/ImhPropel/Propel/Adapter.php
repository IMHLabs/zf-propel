<?php
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Propel/Adapter
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
	
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Propel/Adapter
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
namespace ImhPropel\Propel;

use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class Adapter implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocator
     */
    protected $_serviceLocator;

    /**
     * @var string Module name
     */
    protected $_moduleName;

    /**
     * @var string Data Directory
     */
    protected $_data_dir;

    /**
     * @var string Module Path
     */
    protected $_module_path;

    /**
     * @var string Module Config Path
     */
    protected $_module_config_path;

    /**
     * @var string Module Sql Path
     */
    protected $_module_sql_path;

    /**
     * @var string Module Schema Path
     */
    protected $_module_schema_path;

    /**
     * @var string Module Class Path
     */
    protected $_module_class_path;
     
    /**
     * @var string Module Migration Path
     */
    protected $_module_migration_path;
    
	public function __construct(ServiceLocatorInterface $serviceLocator)
	{
		$this->setServiceLocator($serviceLocator);
        //Store Data Directory
        $config = $this->getServiceLocator()->get('config');
        $config = $config['propel'];
        $this->_data_dir = $config['data_directory'];
	}
    
    /**
	 * Set module Name
	 *
     * @param string $module Module Name
	 * @return void
	 */
    public function setModule($moduleName)
    {
        $this->_moduleName              = $moduleName;
        
        $modulemanager                  = $this->getServiceLocator()->get('ModuleManager');        
		$moduleObj                      = $modulemanager->loadModule($this->getModuleName());
        $module_config 	                = $moduleObj->getConfig();
        $this->_module_path             = $this->_data_dir . '/' . $moduleName;
        $this->_module_config_path      = $this->_module_path . '/config';
        $this->_module_sql_path         = $this->_module_path . '/sql';
        $this->_module_schema_path      = realpath($module_config['propel']['paths']['schema']);
        $this->_module_class_path       = realpath($module_config['propel']['paths']['class']);
        $this->_module_migration_path   = realpath($module_config['propel']['paths']['migrations']);
        return $this;
	}
    
    /**
	 * Get module Name
	 *
	 * @return string
	 */
    public function getModuleName()
    {
        return $this->_moduleName;
	}
    
    /**
	 * Get Data Directory
	 *
	 * @return string
	 */
    public function getDataDir()
    {
        return $this->_data_dir;
	}
    
    /**
	 * Get Module Path
	 *
	 * @return string
	 */
    public function getModulePath()
    {
        return $this->_module_path;
	}

    /**
	 * Get Module Config Path
	 *
	 * @return string
	 */
    public function getModuleConfigPath()
    {
        return $this->_module_config_path;
	}

    /**
	 * Get Module Sql Path
	 *
	 * @return string
	 */
    public function getModuleSqlPath()
    {
        return $this->_module_sql_path;
	}

    /**
	 * Get Module Schema Path
	 *
     * @param string $module Module Name
	 * @return void
	 */
    public function getModuleSchemaPath()
	{
        return $this->_module_schema_path;
	}
    
    /**
	 * Get Module Class Path
	 *
     * @param string $module Module Name
	 * @return void
	 */
    public function getModuleClassPath()
	{
        return $this->_module_class_path;
	}
    
    /**
	 * Get Module Schema Path
	 *
     * @param string $module Module Name
	 * @return void
	 */
    public function getModuleMigrationPath()
	{
        return $this->_module_migration_path;
	}

    /**
	 * Create application data directories for a module
	 * to store programatically created files
	 *
     * @param string $module Module Name
	 * @return void
	 */
    public function createDataDirectories()
    {
        $data_dir           = $this->getDataDir();
        $module_path        = $this->getModulePath();
        $sql_path           = $this->getModuleSqlPath();
        $config_path        = $this->getModuleConfigPath();
		if (!file_exists($data_dir))      {   mkdir ($data_dir);     }
		if (!file_exists($module_path))   {   mkdir ($module_path);  }
		if (!file_exists($sql_path))      {   mkdir ($sql_path);     }
		if (!file_exists($config_path))   {   mkdir ($config_path);  }
	}
    
	/**
	 * Set Service Locator
	 *
     * @param ServiceLocatorInterface $serviceLocator 
	 * @return ImhPropel/Propel/Adapter
	 */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
        return $this;
    }
	
    /**
	 * Get Service Locator
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->_serviceLocator;
	}
    
    /**
	 * Get module propel Configuration
	 *
     * @param string $module Module Name
	 * @return void
	 */
    protected function _getPropelConfig()
	{
		$modulemanager  = $this->getServiceLocator()->get('ModuleManager');
		$moduleObj 		= $modulemanager->loadModule($this->getModuleName());
        $module_config 	= $moduleObj->getConfig();
        //Check if module has configuration for propel
        if (isset($module_config['propel'])) {
			$module_config          = $module_config['propel'];
			$config                 = $this->getServiceLocator()->get('config');
			$propel_db_connections  = $config['propel']['database']['connections'];
			foreach ($module_config['database']['connections'] as $key => $settings) {
                $propel_db_connections['default'] = ($propel_db_connections['default']) ?: array();
                $propel_db_connections[$key]      = ($propel_db_connections[$key]) ?: array();
				$settings = array_merge(
                    $propel_db_connections['default'],
                    $propel_db_connections[$key],
                    $settings
                );
                $settings['adapter']              = ($settings['adapter']) ?: 'mysql';
                $settings['host']                 = ($settings['host'])    ?: 'localhost';
                $settings['dbname']               = ($settings['dbname'])  ?: $key;
				if (!isset($settings['dsn'])) {
                    switch ($settings['adapter']) {
                        case 'mysql':
                            $settings['dsn'] = sprintf(
                                "mysql:host=%s;dbname=%s",
                                $settings['host'],
                                $settings['dbname']
                            );
                            break;
                        case 'oci':
                            $settings['dsn'] = sprintf(
                                "oci:dbname=//%s/%s",
                                $settings['host'],
                                $settings['dbname']
                            );
                            break;
                        case 'pgsql':
                            $settings['dsn'] = sprintf(
                                "pgsql:host=%s;port=5432;dbname=%s;user=%s;password=%s",
                                $settings['host'],
                                $settings['dbname'],
                                $settings['user'],
                                $settings['password']
                            );
                            break;
                    }
                }
                //Unset Module specific settings, not needed for propel configuration
                unset($settings['host'],$settings['dbname']);
				$module_config['database']['connections'][$key] = $settings;
            }
			if ($module_config) {
                //Unset Module specific settings, not needed for propel configuration
				unset($module_config['paths'], $module_config['data_directory']);
				$module_config = array('propel' => $module_config);
                return $module_config;
            }
        }
        return array();
	}
}
