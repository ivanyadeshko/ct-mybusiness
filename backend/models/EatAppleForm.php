<?php

namespace backend\models;

use common\models\Apple;
use services\AppleService;
use yii\base\Model;

/**
 * Eat apple form
 */
class EatAppleForm extends Model
{
    public $id;
    public $size;

    private $_apple;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'size'], 'required'],
            [['id', 'size'], 'integer'],
            ['id', 'validateCanEat'],
            ['size', 'number', 'min' => 1, 'max' => 100],
            ['size', 'validateSize'],
        ];
    }


    /**
     * Validates eat size
     *
     * @param string $attribute
     * @param array $params
     */
    public function validateCanEat($attribute, $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $apple = $this->getApple();
        if (!$apple) {
            $this->addError($attribute, 'Apple is not exits');
            return;
        }

        if (!$apple->canEat()) {
            $msg = $apple->isOnTree()
                ? 'Apple on the tree'
                : !$apple->isFresh() ? 'The apple is spoiled' : 'Unidentified reason';
            $this->addError($attribute, $msg);
        }
    }


    /**
     * Validates eat size
     *
     * @param string $attribute
     * @param array $params
     */
    public function validateSize($attribute, $params): void
    {
        if ($this->hasErrors()) {
            return;
        }
        $apple = $this->getApple();
        if ($apple && ($apple->size - $this->size / 100 < 0)) {
            $this->addError($attribute, 'Incorrect Eat Size');
        }
    }


    /**
     * Eating apple model
     *
     * @return bool
     */
    public function eat(): bool
    {
        if ($this->validate()) {
            return (new AppleService($this->getApple()))->eat($this->size);
        }
        return false;
    }


    /**
     * @return Apple|null
     */
    public function getApple(): ?Apple
    {
        if ($this->_apple === null) {
            $this->_apple = Apple::findOne($this->id);
        }
        return $this->_apple;
    }
}
