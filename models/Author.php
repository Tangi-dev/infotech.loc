<?php

namespace app\models;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "authors".
 *
 * @property int $id
 * @property string $last_name
 * @property string $first_name
 * @property string|null $middle_name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property BookAuthor[] $bookAuthors
 * @property Book[] $books
 * @property Subscription[] $subscriptions
 * @property User[] $users
 */
class Author extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'authors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['middle_name'], 'default', 'value' => null],
            [['last_name', 'first_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['last_name', 'first_name', 'middle_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'last_name' => 'Фамилия',
            'first_name' => 'Имя',
            'middle_name' => 'Отчество',
            'fullName' => 'ФИО',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Books]].
     * @throws InvalidConfigException
     *
     * @return ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('book_author', ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Subscriptions]].
     *
     * @return ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     * @throws InvalidConfigException
     *
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('guest_subscriptions', ['author_id' => 'id']);
    }

    /**
     * Список авторов для выпадающего списка
     *
     * @return array
     */
    public static function getList()
    {
        return ArrayHelper::map(self::find()->orderBy('last_name, first_name')->all(), 'id', 'fullName');
    }

    /**
     * ФИО
     *
     * @return string
     */
    public function getFullName()
    {
        $name = $this->last_name . ' ' . $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }

        return $name;
    }

    /**
     * Берем топ авторовв
     *
     * @param int $startYear
     * @param int $endYear
     * @param int $limit
     * @return array
     */
    public static function getTopAuthorsByYearRange($startYear, $endYear = 1900, $limit = 10)
    {
        $query = (new Query())
            ->select([
                'a.id as author_id',
                'a.first_name',
                'a.last_name',
                'a.middle_name',
                'b.year',
            ])
            ->from(['a' => self::tableName()])
            ->innerJoin('book_author ba', 'ba.author_id = a.id')
            ->innerJoin('books b', 'b.id = ba.book_id')
            ->where(['between', 'b.year', $endYear, $startYear])
            ->groupBy(['a.id', 'b.year']);

        $rawData = $query->all();
        $result = [];
        foreach ($rawData as $row) {
            $year = $row['year'];
            $authorArray = [
                'id' => $row['author_id'],
                'fullName' => $row['last_name'] . ' ' . $row['first_name']
                    . ($row['middle_name'] ? ' ' . $row['middle_name'] : ''),
            ];

            if (!isset($result[$year])) {
                $result[$year] = [];
            }
            $result[$year][] = $authorArray;
        }

        return $result;
    }
}
