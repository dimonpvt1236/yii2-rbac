<?php

namespace nullref\rbac\forms;

use nullref\rbac\components\DbManager;
use nullref\rbac\repositories\AuthItemRepository;

class RoleForm extends ItemForm
{
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
        return parent::attributeLabels();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return parent::rules();
    }

    /**
     * @return array
     */
    public function getUnassignedItems()
    {
        return $this->repository->getUnassignedItems($this->item);
    }

    protected function createItem($name)
    {
        return $this->manager->createRole($name);
    }
}