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
use Propel\Generator\Manager\MigrationManager;

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
			    VENDOR_PATH,
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
			    VENDOR_PATH,
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
        if ($configCreated) {
			$propel_command			= sprintf(
    	        '%s/bin/propel migration:diff --config-dir="%s" --schema-dir="%s" --output-dir="%s"',
			    VENDOR_PATH,
			    $this->getModuleConfigPath(),
				$this->getModuleSchemaPath(),
				$this->getModuleMigrationPath());
			$results = exec($propel_command,$output);
            print "Building Migrations Files for Module: " . $this->getModuleName() . "\n";
            print implode("\n",$output);
            if (preg_match('/no diff to generate/',$results)) {
                return false;
            }
            return true;
		}
	}
    
    /**
	 * Generate Empty Migration File for data migration
	 *
	 * @return void
	 */
    public function dataMigration()
	{
        $migrationGenerated = $this->diff();
        if (!$migrationGenerated) {
            print "\nBuilding Data Migration File for Module: " . $this->getModuleName() . "\n";
            //Generate Data Migration File Here
            $manager = new MigrationManager();
            $timestamp = time();
            $migrationFileName  = $manager->getMigrationFileName($timestamp);
            $migrationClassBody = $manager->getMigrationClassBody(array(), array(), $timestamp, null);
            $migration_path         = $this->getModuleMigrationPath();
            $filename = $migration_path . '/' . $migrationFileName;
            file_put_contents($filename, $migrationClassBody); 
            print "\n" . realpath($filename) . " file successfully created for data migration\n";
            print "Once the migration class is valid, call the \"migrate\" task to execute it.\n";
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
        if ($configCreated) {
			$propel_command			= sprintf(
    	        '%s/bin/propel migration:down --config-dir="%s" --output-dir="%s"',
			    VENDOR_PATH,
			    $this->getModuleConfigPath(),
				$this->getModuleMigrationPath());
			$results = exec($propel_command,$output);
            print "Executing Propel Down for Module: " . $this->getModuleName() . "\n";
            print implode("\n",$output);
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
        if ($configCreated) {
			$propel_command			= sprintf(
				'%s/bin/propel migrate --config-dir="%s" --output-dir="%s"',
			    VENDOR_PATH,
			    $this->getModuleConfigPath(),
				$this->getModuleMigrationPath());
			$results = exec($propel_command,$output);
            print "Executing Propel Migrations for Module: " . $this->getModuleName() . "\n";
            print implode("\n",$output);
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
        if ($configCreated) {
			$propel_command			= sprintf(
    	        '%s/bin/propel migration:status --config-dir="%s" --output-dir="%s"',
			    VENDOR_PATH,
			    $this->getModuleConfigPath(),
				$this->getModuleMigrationPath());
			$results = exec($propel_command,$output);
            print "Executing Propel Status for Module: " . $this->getModuleName() . "\n";
            print implode("\n",$output);
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
        if ($configCreated) {
			$propel_command			= sprintf(
    	        '%s/bin/propel migration:up --config-dir="%s" --output-dir="%s"',
			    VENDOR_PATH,
			    $this->getModuleConfigPath(),
				$this->getModuleMigrationPath());
			$results = exec($propel_command,$output);
            print "Executing Propel Up for Module: " . $this->getModuleName() . "\n";
            print implode("\n",$output);
		}
	}
}
