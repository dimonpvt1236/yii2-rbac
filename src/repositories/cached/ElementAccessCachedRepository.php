<?php

namespace nullref\rbac\repositories\cached;

use nullref\rbac\ar\ElementAccess;
use nullref\rbac\forms\ElementAccessForm;
use nullref\rbac\repositories\ElementAccessRepository;
use nullref\rbac\repositories\interfaces\ElementAccessRepositoryInterface;
use yii\caching\TagDependency;

class ElementAccessCachedRepository extends AbstractCachedRepository implements ElementAccessRepositoryInterface
{
    /** @var ElementAccessRepository */
    protected $repository;

    /**
     * ElementAccessCachedRepository constructor.
     *
     * @param ElementAccessRepository $repository
     */
    public function __construct(
        ElementAccessRepository $repository
    )
    {
        $this->repository = $repository;

        parent::__construct($repository);
    }

    public function findItems($identifier)
    {
        $ar = $this->repository->getAr();
        $items = $ar::getDb()->cache(
            function () use ($identifier) {
                return $this->repository->findItems($identifier);
            },
            null,
            new TagDependency(['tags' => $identifier . '-element-items'])
        );

        return $items;
    }

    public function saveWithItems(ElementAccessForm $form)
    {
        $result = $this->repository->saveWithItems($form);

        if ($result) {
            $this->invalidate($form->identifier . '-element-items');
        }

        return $result;
    }

    public function updateWithItems(ElementAccessForm $form, ElementAccess $elementAccess)
    {
        $this->invalidate($elementAccess->identifier . '-element-items');
        $result = $this->repository->updateWithItems($form, $elementAccess);

        return $result;
    }

    public function delete($condition)
    {
        $model = $this->repository->findOneByCondition($condition);
        if ($model) {
            $this->invalidate($model->identifier . '-element-items');
        }
        $this->repository->delete($condition);
    }
}
