<?php

namespace nullref\rbac;

use nullref\core\interfaces\IAdminModule;
use nullref\core\interfaces\IHasMigrateNamespace;
use nullref\rbac\ar\User;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\i18n\PhpMessageSource;
use yii\web\Application as WebApplication;

/**
 * Class Module
 *
 * @package nullref\category
 */
class Module extends BaseModule implements IAdminModule, IHasMigrateNamespace
{
    /** @var array  */
    public $controllerAliases = [
        '@nullref/rbac/controllers',
    ];

    /**
     * @var string
     */
    public $userActiveRecordClass = User::class;

    /** @var string */
    public $userComponent = null;

    /** @var array */
    public $classMap = [];

    /**
     * @var array
     */
    public $defaultClassMap = [
        //TODO
        'Category'      => 'nullref\category\models\Category',
        'CategoryQuery' => 'nullref\category\models\CategoryQuery',
    ];

    /**
     * Item for admin menu
     * @return array
     */
    public static function getAdminMenu()
    {
        return [
            'label' => Yii::t('rbac', 'Access control'),
            'icon'  => FA::_WRENCH,
            'order' => 6,
            'items' => [
                [
                    'label' => Yii::t('rbac', 'Actions access'),
                    'icon'  => FA::_MAP_SIGNS,
                    'url'   => '/rbac/access/',
                ],
                [
                    'label' => Yii::t('rbac', 'Auth items'),
                    'icon'  => FA::_GEARS,
                    'url'   => '/rbac/auth-item/',
                ],
                [
                    'label' => Yii::t('rbac', 'Roles'),
                    'icon'  => FA::_GEAR,
                    'url'   => '/rbac/role/',
                ],
                [
                    'label' => Yii::t('rbac', 'Permissions'),
                    'icon'  => FA::_GEAR,
                    'url'   => '/rbac/permission/',
                ],
                [
                    'label' => Yii::t('rbac', 'Rules'),
                    'icon'  => FA::_GAVEL,
                    'url'   => '/rbac/rule/',
                ],
            ],
        ];
    }

    /**
     * Return path to folder with migration with namespaces
     *
     * @param $defaults
     *
     * @return array
     */
    public function getMigrationNamespaces($defaults)
    {
        return ['nullref\rbac\migration_ns'];
    }
}