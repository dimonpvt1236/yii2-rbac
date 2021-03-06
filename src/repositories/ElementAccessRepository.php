<?php

namespace nullref\rbac\repositories;

use nullref\rbac\ar\ElementAccess;
use nullref\rbac\ar\ElementAccessItem;
use nullref\rbac\forms\ElementAccessForm;
use nullref\rbac\repositories\interfaces\ElementAccessRepositoryInterface;

class ElementAccessRepository extends AbstractRepository implements ElementAccessRepositoryInterface
{
    /** @var ElementAccessItemRepository */
    private $elementAccessItemRepository;

    /**
     * ElementAccessRepository constructor.
     *
     * @param $activeRecord string
     * @param ElementAccessItemRepository $elementAccessItemRepository
     */
    public function __construct(
        ElementAccessItemRepository $elementAccessItemRepository,
        $activeRecord
    )
    {
        $this->elementAccessItemRepository = $elementAccessItemRepository;

        parent::__construct($activeRecord);
    }

    public function findOneWithAuthItems($id)
    {
        return $this->ar::find()
            ->andWhere(['id' => $id])
            ->with(['authItems'])
            ->one();
    }

    public function findItems($identifier)
    {
        $element = $this->findOneByCondition(['identifier' => $identifier]);
        if ($element) {
            return $this->elementAccessItemRepository->findItems($element->id);
        }

        return [];
    }

    public function assignItems($elementId, $items)
    {
        if (!is_array($items)) {
            $items = [];
        }

        $oldItems = $this->elementAccessItemRepository->findItems($elementId);

        //Add new items
        foreach (array_diff($items, $oldItems) as $itemName) {
            $newItem = new ElementAccessItem([
                'element_access_id' => $elementId,
                'auth_item_name'    => $itemName,
            ]);
            $this->elementAccessItemRepository->save($newItem);
        }

        //Remove items
        $itemsToRemove = [];
        foreach (array_diff($oldItems, $items) as $itemName) {
            $itemsToRemove[] = $itemName;
        }

        $this->elementAccessItemRepository->delete([
            'auth_item_name'    => $itemsToRemove,
            'element_access_id' => $elementId,
        ]);

        return true;
    }

    public function saveWithItems(ElementAccessForm $form)
    {
        $elementAccess = new ElementAccess([
            'identifier'  => $form->identifier,
            'description' => $form->description,
        ]);
        if ($this->save($elementAccess)) {
            $this->assignItems($elementAccess->id, $form->items);

            return $elementAccess->id;
        }

        return false;
    }

    public function updateWithItems(ElementAccessForm $form, ElementAccess $elementAccess)
    {
        $elementAccess->identifier = $form->identifier;
        $elementAccess->description = $form->description;
        if ($this->save($elementAccess)) {
            $this->assignItems($elementAccess->id, $form->items);

            return $elementAccess->id;
        }

        return false;
    }
}
