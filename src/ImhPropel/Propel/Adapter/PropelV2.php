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
namespace ImhPropel\Propel\Adapter;

use ImhPropel\Propel\Adapter;
use ImhPropel\Propel\Generator\Manager\MigrationManager;
use Propel\Runtime\Propel;

class PropelV2 extends Adapter
{
    /**
	 * Create Configuration file for module
	 *
	 * @return boolean
	 */
    public function createConfig()
	{
        $config = $this->_getPropelConfig();
        if ($config) {
            $this->createDataDirectories();
			$config_path = $this->getModuleConfigPath() . '/propel.json';
            file_put_contents ($config_path, json_encode($config));        
            return true;
        }
        return false;
	}

    
    
    /**
	 * Build the model classes based on Propel XML schemas
	 *
	 * @return void
	 */
    public function build()
	{
        $configCreated = $this->createConfig();
        if ($configCreated) {
			$propel_command			= sprintf(
				'%s/bin/propel model:build --config-dir="%s" --schema-dir="%s" --output-dir="%s"',
			    $this->_getVendorPath(),
			    $this->getModuleConfigPath(),
				$this->getModuleSchemaPath(),
				$this->getModuleClassPath());
			exec($propel_command,$output);
            print "Building Model Classes for Module: " . $this->getModuleName() . "\n";
            print implode("\n",$output);
		}
	}
    
    /**
	 * Build SQL files
	 *
	 * @return void
	 */
    public function buildSql()
	{
        $configCreated = $this->createConfig();
        if ($configCreated) {
			$propel_command	= sprintf(
				'%s/bin/propel sql:build --config-dir="%s" --schema-dir="%s" --output-dir="%s" --overwrite',
			    $this->_getVendorPath(),
			    $this->getModuleConfigPath(),
				$this->getModuleSchemaPath(),
				$this->getModuleSqlPath());
			exec($propel_command,$output);
            print "Building SQL Files for Module: " . $this->getModuleName() . "\n";
            print implode("\n",$output);
		}
	}
    
    /**
	 * Generate diff classes
	 *
	 * @return boolean
	 */
    public function diff()
	{
	    $configCreated = $this->createConfig();
        if ($this->getModuleMigrationPath()) {
    	    if ($configCreated) {
    	        $excludedTables = array();
    	        $modulemanager  = $this->getServiceLocator()->get('ModuleManager');
    	        $moduleObj 		= $modulemanager->loadModule($this->getModuleName());
                if (method_exists ( $moduleObj, 'getConfig')) {
        	        $module_config 	= $moduleObj->getConfig();
        	        $connections = array_keys($module_config['propel']['database']['connections']);
        	        //Get List of Modules pointing at database of current Module
        	        $modules = $modulemanager->getModules();
        	        foreach ($modules as $module) {
        	            if ($module != $this->getModuleName()) {
        	                $moduleObj 		= $modulemanager->loadModule($module);
                            if (method_exists ( $moduleObj, 'getConfig')) {
            	                $module_config 	= $moduleObj->getConfig();
            	                if (isset($module_config['propel'])) {
            	                    $module_connections = array_keys($module_config['propel']['database']['connections']);
            	                    if (array_intersect($connections, $module_connections)) {
            	                        // Get Module Schema Path
            	                        $module_schema_path      = realpath($module_config['propel']['paths']['schema']);
            	                        //Used same connection, Read schema
            	                        $xml = (array) simplexml_load_file($module_schema_path . '/schema.xml');
            	                        foreach ($xml['table'] as $table) {
            	                            $table = (array) $table;
            	                            $attributes = $table['@attributes'];
            	                            $excludedTables[] = $attributes['name'];
            	                        }
            	                    }
            	                }
                            }
        	            }
        	        }
        	        $propel_command			= sprintf(
        	                '%s/bin/propel migration:diff --config-dir="%s" --schema-dir="%s" --output-dir="%s"',
        	                $this->_getVendorPath(),
        	                $this->getModuleConfigPath(),
        	                $this->getModuleSchemaPath(),
        	                $this->getModuleMigrationPath());
        	        foreach ($excludedTables as $table) {
        	            $propel_command .= ' --skip-tables="' . $table . '"';
        	        }
        	        $results = exec($propel_command,$output);
        	        print "Building Migrations Files for Module: " . $this->getModuleName() . "\n";
        	        print implode("\n",$output);
        	        if (preg_match('/no diff to generate/',$results)) {
        	            return false;
        	        }
                }
    	        return true;
    	    }
        } else {
	       print "Migrations not supported for Module: " . $this->getModuleName() . "\n";
        }
    }
    
    /**
	 * Generate Empty Migration File for data migration
	 *
	 * @return void
	 */
    public function dataMigration()
	{
        if ($this->getModuleMigrationPath()) {
    	    print "\nBuilding Data Migration File for Module: " . $this->getModuleName() . "\n";
            $manager = new MigrationManager();
            $timestamp = time();
            $migrationFileName  = $manager->getMigrationFileName($timestamp);
            $migrationQuery = array(
                $this->getModuleDbConnection() => '
                    # This is a fix for InnoDB in MySQL >= 4.1.x
                    # It "suspends judgement" for fkey relationships until are tables are set.
                    SET FOREIGN_KEY_CHECKS = 0;
    
                    # This restores the fkey checks, after having unset them earlier
                    SET FOREIGN_KEY_CHECKS = 1;
                    ',
             );
            $migrationClassBody = $manager->getMigrationClassBody($migrationQuery, $migrationQuery, $timestamp, null,  $this->getModuleName());
            $migration_path         = $this->getModuleMigrationPath();
            $filename = $migration_path . '/' . $migrationFileName;
            //Generate Data Migration File Here
            file_put_contents($filename, $migrationClassBody); 
            print "\n" . realpath($filename) . " file successfully created for data migration\n";
            print "Once the migration class is valid, call the \"migrate\" task to execute it.\n";
        } else {
	       print "Migrations not supported for Module: " . $this->getModuleName() . "\n";
        }
    }

    /**
	 * Execute migrations down
	 *
	 * @return void
	 */
    public function down()
	{
        $configCreated = $this->createConfig();
        if ($this->getModuleMigrationPath()) {
            if ($configCreated) {
    			$propel_command			= sprintf(
        	        '%s/bin/propel migration:down --config-dir="%s" --output-dir="%s"',
    			    $this->_getVendorPath(),
    			    $this->getModuleConfigPath(),
    				$this->getModuleMigrationPath());
    			$results = exec($propel_command,$output);
                print "Executing Propel Down for Module: " . $this->getModuleName() . "\n";
                print implode("\n",$output);
    		}
        } else {
        	print "Migrations not supported for Module: " . $this->getModuleName() . "\n";
        }
    }
    
    /**
	 * Execute all pending migrations
	 *
	 * @return void
	 */
    public function migrate()
	{
        $configCreated = $this->createConfig();
        if ($this->getModuleMigrationPath()) {
            if ($configCreated) {
    			$propel_command			= sprintf(
    				'%s/bin/propel migrate --config-dir="%s" --output-dir="%s"',
    			    $this->_getVendorPath(),
    			    $this->getModuleConfigPath(),
    				$this->getModuleMigrationPath());
    			$results = exec($propel_command,$output);
                print "Executing Propel Migrations for Module: " . $this->getModuleName() . "\n";
                print implode("\n",$output);
    		}
        } else {
            print "Migrations not supported for Module: " . $this->getModuleName() . "\n";
        }
	}
    
    /**
	 * Get migration status 
	 *
	 * @return void
	 */
    public function status()
	{
	    $configCreated = $this->createConfig();
        if ($this->getModuleMigrationPath()) {
    	    if ($configCreated) {
    			$propel_command			= sprintf(
        	        '%s/bin/propel migration:status --verbose --config-dir="%s" --output-dir="%s"',
    			    $this->_getVendorPath(),
    			    $this->getModuleConfigPath(),
    				$this->getModuleMigrationPath());
    			$results = exec($propel_command,$output);
                print "Executing Propel Status for Module: " . $this->getModuleName() . "\n";
                print implode("\n",$output);
                return $output;
    		}
        } else {
	       print "Migrations not supported for Module: " . $this->getModuleName() . "\n";
        }
    }

    /**
	 * Execute migrations up
	 *
	 * @return void
	 */
    public function up()
	{
        $configCreated = $this->createConfig();
        if ($this->getModuleMigrationPath()) {
            if ($configCreated) {
    			$propel_command			= sprintf(
        	        '%s/bin/propel migration:up --config-dir="%s" --output-dir="%s"',
    			    $this->_getVendorPath(),
    			    $this->getModuleConfigPath(),
    				$this->getModuleMigrationPath());
    			$results = exec($propel_command,$output);
                print "Executing Propel Up for Module: " . $this->getModuleName() . "\n";
                print implode("\n",$output);
    		}
        } else {
	       print "Migrations not supported for Module: " . $this->getModuleName() . "\n";
        }
    }
}
