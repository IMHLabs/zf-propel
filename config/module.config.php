<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'ImhPropel\Controller\Propel' => 'ImhPropel\Controller\PropelController'
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'propel-build' => array(
                    'options' => array(
                        'route'    => 'propel build <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'build'
                        )
                    )
                ),
                'propel-build-sql' => array(
                    'options' => array(
                        'route'    => 'propel build-sql <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'build-sql'
                        )
                    )
                ),
                'propel-diff' => array(
                    'options' => array(
                        'route'    => 'propel diff <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'diff'
                        )
                    )
                ),
                'propel-data-migration' => array(
                    'options' => array(
                        'route'    => 'propel migration-data <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'data-migration'
                        )
                    )
                ),
                'propel-migration-data' => array(
                    'options' => array(
                        'route'    => 'propel migrate-data <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'data-migration'
                        )
                    )
                ),
                'propel-down' => array(
                    'options' => array(
                        'route'    => 'propel down <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'down'
                        )
                    )
                ),
                'propel-migrate' => array(
                    'options' => array(
                        'route'    => 'propel migrate <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'migrate'
                        )
                    )
                ),
                'propel-status' => array(
                    'options' => array(
                        'route'    => 'propel status <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'status'
                        )
                    )
                ),
                'propel-up' => array(
                    'options' => array(
                        'route'    => 'propel up <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'up'
                        )
                    )
                ),
                'propel-migration-diff' => array(
                    'options' => array(
                        'route'    => 'propel migration-diff <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'diff'
                        )
                    )
                ),
                'propel-migration-down' => array(
                    'options' => array(
                        'route'    => 'propel migration-down <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'down'
                        )
                    )
                ),
                'propel-migration-migrate' => array(
                    'options' => array(
                        'route'    => 'propel migration-migrate <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'migrate'
                        )
                    )
                ),
                'propel-migration-status' => array(
                    'options' => array(
                        'route'    => 'propel migration-status <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'status'
                        )
                    )
                ),
                'propel-migration-up' => array(
                    'options' => array(
                        'route'    => 'propel migration-up <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'up'
                        )
                    )
                ),
                'propel-model-build' => array(
                    'options' => array(
                        'route'    => 'propel model-build <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'build'
                        )
                    )
                ),
                'propel-sql-build' => array(
                    'options' => array(
                        'route'    => 'propel sql-build <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'build-sql'
                        )
                    )
                ),
                'propel-update' => array(
                    'options' => array(
                        'route'    => 'propel update <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'update'
                        )
                    )
                ),
                'propel-migration' => array(
                    'options' => array(
                        'route'    => 'propel migration <namespace>',
                        'defaults' => array(
                            'controller' => 'ImhPropel\Controller\Propel',
                            'action'     => 'diff'
                        )
                    )
                )
            ),
        ),
    ),
);
