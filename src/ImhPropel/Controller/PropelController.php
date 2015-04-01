<?php
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Controller/PropelController
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
	
/**
 * InMotion Hosting Source Code
 * @package ImhPropel/Controller/PropelController
 * @copyright Copyright (c) InMotion Hosting
 * @version $Id$
 * @author IMH Development <development@inmotionhosting.com>
 */
namespace ImhPropel\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class PropelController extends AbstractActionController
{
    /**
	 * Build the model classes based on Propel XML schemas
	 *
	 * @return void
	 */
    public function buildAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->build();
			}
		}
    }

    /**
	 * Build SQL files
	 *
	 * @return void
	 */
    public function buildSqlAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->buildSql();
			}
		}
    }
    
    /**
	 * Generate diff classes
	 *
	 * @return void
	 */
    public function diffAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->diff();
			}
		}
    }

    /**
	 * Execute migrations down
	 *
	 * @return void
	 */
    public function downAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->down();
			}
		}
    }
    
    /**
	 * Execute all pending migrations
	 *
	 * @return void
	 */
    public function migrateAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->migrate();
			}
		}
    }

    /**
	 * Execute all pending migrations
	 *
	 * @return void
	 */
    public function dataMigrationAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->dataMigration();
			}
		}
    }

    /**
	 * Get migration status 
	 *
	 * @return array
	 */
    public function statusAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->status();
			}
		}
    }

    /**
	 * Execute migrations up 
	 *
	 * @return array
	 */
    public function upAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->up();
			}
		}
    }

    /**
	 * Execute migrations up From a timestamp
	 *
	 * @return array
	 */
    public function upFromAction()
    {
        $modules    = $this->_getModules();
        $request        		= $this->getRequest();
        $timestamp  = $request->getParam('timestamp');
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $status = $propelAdapter->status();
                $migrations = array();
                foreach ($status as $migration) {
                    if (preg_match('/^\s+PropelMigration/',$migration)) {
                        $migration = preg_replace('/\D/','',$migration);
                        if ($migration > $timestamp) {
                            $migrations[] = $migration; 
                        }
                    }
                }
                foreach ($migrations as $migration) {
                    $propelAdapter->down();
                }
                $propelAdapter->migrate();
			}
		}
    }
    
    /**
	 * Execute all updates for module(s)
	 *
	 * @return void
	 */
    public function updateAction()
    {
        $modules = $this->_getModules();
        foreach ($modules as $module) {
            $propelAdapter    = $this->_getAdapter($module);
            if ($propelAdapter) {
                $propelAdapter->migrate();
                $propelAdapter->buildSql();
                $propelAdapter->build();
			}
		}
    }
    
    
    /*    
    
    /**
	 * Get Module to update
	 *
	 * @return void
	 */
    protected function _getModules()
    {
        $return = array();
        $request        		= $this->getRequest();
        $namespace      		= $request->getParam('namespace');
        if ($namespace == 'all') {
            $modulemanager  = $this->getServiceLocator()->get('ModuleManager');        
            $modules = $modulemanager->getLoadedModules();
            $return = array_keys($modules);
		} else {
            if (preg_match('/,/',$namespace)) {
                $return = explode(',',$namespace);
            } else {
                $return = array($namespace);
            }
        }
        return $return;
    }

    /**
	 * Get Module to update
	 *
	 * @return void
	 */
    protected function _getAdapter($module)
    {
		$modulemanager  = $this->getServiceLocator()->get('ModuleManager');
		$moduleObj 		= $modulemanager->loadModule($module);
        if (method_exists ( $moduleObj, 'getConfig')) {
    		$module_config 	= $moduleObj->getConfig();
            //Check if module has configuration for propel
            if (isset($module_config['propel'])) {
                $propelAdapter = new \ImhPropel\Propel\Adapter\PropelV2($this->getServiceLocator());
                $propelAdapter->setModule($module);
                return $propelAdapter;
            }
        }
        return null;
    }
}
