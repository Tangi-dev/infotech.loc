<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * BookSearch
 */
class bookSearch extends Model
{
    public $id;
    public $title;
    public $year;
    public $isbn;
    public $authorIds = [];
    public $authorName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year'], 'integer'],
            [['title', 'isbn', 'authorName'], 'string'],
            [['authorIds'], 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год выпуска',
            'isbn' => 'ISBN',
            'authorIds' => 'Авторы',
            'authorName' => 'Имя автора',
        ];
    }

    /**
     * Запрос для поиска книг
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Book::find()->joinWith('authors');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
                'attributes' => [
                    'id',
                    'title',
                    'year',
                    'isbn',
                    'created_at',
                    'authors' => [
                        'asc' => ['authors.last_name' => SORT_ASC],
                        'desc' => ['authors.last_name' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        // Поправил ошибку валидации
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // Фильтры
        if ($this->id) {
            $query->andWhere(['books.id' => $this->id]);
        }
        if ($this->title) {
            $query->andWhere(['like', 'books.title', $this->title]);
        }
        if ($this->year) {
            $query->andWhere(['books.year' => $this->year]);
        }
        if ($this->isbn) {
            $query->andWhere(['like', 'books.isbn', $this->isbn]);
        }
        if (!empty($this->authorIds)) {
            $query->andWhere(['authors.id' => $this->authorIds]);
        }
        if ($this->authorName) {
            $query->andWhere(['or',
                ['like', 'authors.last_name', $this->authorName],
                ['like', 'authors.first_name', $this->authorName],
                ['like', 'authors.middle_name', $this->authorName],
            ]);
        }

        return $dataProvider;
    }

    /**
     * Список годов (для фильтра)
     * @return array
     */
    public static function getYearsList()
    {
        return ArrayHelper::map(
            Book::find()
                ->select('year')
                ->distinct()
                ->orderBy('year DESC')
                ->all(),
            'year',
            'year'
        );
    }

    /**
     * Список авторов (для фильтра)
     * @return array
     */
    public static function getAuthorsList()
    {
        return ArrayHelper::map(
            Author::find()
                ->select(['id', 'last_name', 'first_name', 'middle_name'])
                ->orderBy('last_name, first_name')
                ->all(),
            'id',
            function ($author) {
                return $author->getFullName();
            }
        );
    }
}