<?php

namespace common\models;

use yii\base\InvalidValueException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%apple}}".
 *
 * @property int $id
 * @property string $color
 * @property float $size
 * @property int $fall_at
 * @property int $created_at
 */
class Apple extends \yii\db\ActiveRecord
{

    /**
     * The time the apple stays fresh
     */
    const FRESH_TIME = 5 * 3600;


    const COLOR_GREEN = 'green';
    const COLOR_YELLOW = 'yellow';
    const COLOR_RED = 'red';
    const COLOR_PINK = 'pink';


    /**
     * @TODO Для соответствия с ТЗ (создание модели с передачей строки в конструктор), пришлось переопределить логику конструктора.
     * Это топорная реализация.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (is_string($config)) {
            $config = [
                'color' => $config
            ];
        }
        parent::__construct($config);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            TimestampBehavior::class => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%apple}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color', 'size'], 'required'],
            [['size'], 'number'],
            [['fall_at', 'created_at'], 'integer'],
            [['color'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color' => 'Color',
            'size' => 'Size',
            'fall_at' => 'Fall At',
            'created_at' => 'Created At',
        ];
    }




    /**
     * @return array
     */
    public static function getColors(): array
    {
        return [
            self::COLOR_GREEN,
            self::COLOR_RED,
            self::COLOR_YELLOW,
            self::COLOR_PINK,
        ];
    }


    /**
     * @return bool
     */
    public function fallToGround(): bool
    {
        if (!$this->isOnTree()) {
            throw new InvalidValueException("The apple is already on ground");
        }
        $this->fall_at = time();
        return true;
    }


    /**
     * @param int $percent
     * @return bool
     */
    public function eat(int $percent): bool
    {
        if (!$this->canEat()) {
            throw new InvalidValueException("The apple is already on ground");
        }
        $this->size -= $percent/100;
        if ($this->size < 0) {
            throw new InvalidValueException("Can't eat more then apple size");
        }
        return true;
    }


    /**
     * Set default values into model
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->size = 1;
        }
    }


    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert && $this->isEaten()) {
            $this->delete();
        }
    }


    /**
     *
     * @return bool
     */
    public function isOnTree(): bool
    {
        return $this->fall_at === null;
    }

    /**
     *
     * @return bool
     */
    public function isFresh(): bool
    {
        return $this->isOnTree() || ($this->fall_at + self::FRESH_TIME) > time();
    }


    /**
     * Checking if we cat eat the apple
     *
     * @return bool
     */
    public function canEat(): bool
    {
        return !$this->isOnTree() && $this->isFresh();
    }

    /**
     * @return bool
     */
    public function isEaten(): bool
    {
        return $this->size <= 0;
    }

}
