<?php

namespace nullref\rbac;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\ActionAccessItem;
use nullref\rbac\ar\AuthItem;
use nullref\rbac\ar\AuthItemChild;
use nullref\rbac\ar\AuthRule;
use nullref\rbac\components\DbManager;
use nullref\rbac\repositories\ActionAccessItemRepository;
use nullref\rbac\repositories\ActionAccessRepository;
use nullref\rbac\repositories\AuthItemChildRepository;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\RuleRepository;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\i18n\PhpMessageSource;
use yii\web\Application as WebApplication;

class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     *
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        /** @var Module $module */
        if ((($module = $app->getModule('rbac')) == null) || !($module instanceof Module)) {
            return;
        };

        if ($module->userActiveRecordClass === null) {
            throw new InvalidConfigException(Module::class . '::userActiveRecordClass has to be set');
        }

        if ($module->userComponent === null) {
            throw new InvalidConfigException(Module::class . '::userComponent has to be set');
        }

        $classMap = array_merge($module->defaultClassMap, $module->classMap);
        //TODO
        foreach (['Category', 'CategoryQuery'] as $item) {
            $className = __NAMESPACE__ . '\models\\' . $item;
            $definition = $classMap[$item];
            Yii::$container->set($className, $definition);
        }

        if ($app instanceof WebApplication) {
            if (!isset($app->i18n->translations['rbac*'])) {
                $app->i18n->translations['rbac*'] = [
                    'class'    => PhpMessageSource::class,
                    'basePath' => '@nullref/rbac/messages',
                ];
            }
        }

        if ($this->checkModuleInstalled($app)) {
            $authManager = $app->get('authManager', false);

            if (!$authManager) {
                $app->set('authManager', [
                    'class' => DbManager::class,
                ]);
            } else if (!($authManager instanceof ManagerInterface)) {
                throw new InvalidConfigException('You have wrong authManager configuration');
            }
        }

        //Set user active record
        Yii::$container->set(User::class, $module->userActiveRecordClass);

        Yii::$container->set(
            ActionAccessRepository::class,
            function ($container, $params, $config) {
                return new ActionAccessRepository(
                    $container->get(ActionAccessItemRepository::class),
                    $container->get(ActionAccess::class)
                );
            }
        );
        Yii::$container->set(
            ActionAccessItemRepository::class,
            function ($container, $params, $config) {
                return new ActionAccessItemRepository(
                    $container->get(ActionAccessItem::class)
                );
            }
        );
        Yii::$container->set(
            AuthItemRepository::class,
            function ($container, $params, $config) {
                return new AuthItemRepository(
                    AuthItem::class,
                    $container->get(DbManager::class)
                );
            }
        );
        Yii::$container->set(
            AuthItemChildRepository::class,
            function ($container, $params, $config) {
                return new AuthItemChildRepository(AuthItemChild::class);
            }
        );
        Yii::$container->set(
            RuleRepository::class,
            function ($container, $params, $config) {
                return new RuleRepository($container->get(AuthRule::class));
            }
        );
    }

    /**
     * Verifies that module is installed and configured.
     *
     * @param  Application $app
     *
     * @return bool
     */
    protected function checkModuleInstalled(Application $app)
    {
        if ($app instanceof WebApplication) {
            return $app->hasModule('rbac') && $app->getModule('rbac') instanceof Module;
        } else {
            return false;
        }
    }
}