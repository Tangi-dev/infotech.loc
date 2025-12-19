<?php
namespace app\models;
use Yii;
use yii\db\ActiveRecord;

class Subscription extends ActiveRecord
{

    public static function tableName()
    {
        return 'subscriptions';
    }

    public function rules()
    {
        return [
            [['phone', 'author_id'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['phone', 'author_id'], 'unique', 'targetAttribute' => ['phone', 'author_id']],
            [['author_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Телефон',
            'author_id' => 'Автор',
            'created_at' => 'Дата подписки',
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * Создаем новую подписку
     *
     * @param string $phone
     * @param int $authorId
     * @param Author|null $author
     *
     * @return array
     */
    public static function createSubscription($phone, $authorId, $author = null)
    {
        if (!preg_match('/^\+7\d{10}$/', $phone)) {
            return [
                'success' => false,
                'message' => 'Неверный формат номера',
                'subscription' => null,
            ];
        }

        $existingSubscription = self::find()
            ->where(['phone' => $phone, 'author_id' => $authorId])
            ->one();
        if ($existingSubscription) {
            $authorName = $author ? $author->fullName : 'автора';
            return [
                'success' => false,
                'message' => "Уже подписаны на $authorName с этого номера телефона.",
                'subscription' => $existingSubscription,
                'alreadyExists' => true,
            ];
        }

        $subscription = new self();
        $subscription->phone = $phone;
        $subscription->author_id = $authorId;

        if ($subscription->save()) {
            return [
                'success' => true,
                'message' => 'Подписка успешно оформлена',
                'subscription' => $subscription,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Ошибка оформлении подписки: ' . implode(', ', $subscription->getErrorSummary(true)),
                'subscription' => $subscription,
                'errors' => $subscription->errors,
            ];
        }
    }
}