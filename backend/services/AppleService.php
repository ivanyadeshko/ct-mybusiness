<?php

namespace services;

use common\exceptions\LogicException;

class AppleService
{

    /**
     * @var \common\models\Apple $apple
     */
    protected $apple;


    public function __construct(\common\models\Apple $apple)
    {
        $this->apple = $apple;
    }


    /**
     * @return bool
     * @throws LogicException
     */
    public function fallToGround(): bool
    {
        if (!$this->apple->isOnTree()) {
            throw new LogicException("The apple is already on ground");
        }
        $this->apple->fall_at = time();
        return $this->apple->save();
    }


    /**
     * @param int $percent
     * @return bool
     * @throws LogicException
     */
    public function eat(int $percent): bool
    {
        if (!$this->apple->canEat($percent)) {
            throw new LogicException("Can't eat the apple");
        }
        $this->apple->size -= $percent / 100;
        return $this->apple->save();
    }


}