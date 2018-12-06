<?php

namespace nullref\rbac\forms;

use nullref\rbac\components\DbManager;
use nullref\rbac\repositories\AuthItemRepository;
use yii\rbac\Item;
use Yii;

class PermissionForm extends ItemForm
{
    /** @var string  */
    public $rule;

    /** @var array|string */
    public $data;

    /** @var AuthItemRepository */
    private $repository;

    /**
     * RoleForm constructor.
     *
     * @param AuthItemRepository $repository
     * @param DbManager $manager
     * @param array $config
     */
    public function __construct(
        AuthItemRepository $repository,
        DbManager $manager,
        $config = []
    )
    {
        $this->repository = $repository;

        parent::__construct($manager, $config);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
           'rule' => Yii::t('rbac', 'Rule'),
           'data' => Yii::t('rbac', 'Data')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'description', 'children', 'rule', 'data'],
            'update' => ['name', 'description', 'children', 'rule', 'data'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['rule'], 'trim'],
            [
                'rule',
                function () {
                    $rule = $this->manager->getRule($this->rule);

                    if (!$rule) {
                        $this->addError('rule', Yii::t('rbac', 'Rule {0} does not exist', $this->rule));
                    }
                },
            ],
            [
                'data',
                function () {
                    try {
                        Json::decode($this->data);
                    } catch (InvalidParamException $e) {
                        $this->addError('data', Yii::t('rbac', 'Data must be type of JSON ({0})', $e->getMessage()));
                    }
                },
            ],
        ]);
    }

    public function getUnassignedItems()
    {
        return $this->repository->getUnassignedItems($this->item, Item::TYPE_PERMISSION);
    }

    protected function createItem($name)
    {
        return $this->manager->createPermission($name);
    }
}