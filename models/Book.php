<?php

namespace app\models;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string $isbn
 * @property string|null $image
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property UploadedFile|null $imageFile
 *
 * @property Author[] $authors
 * @property BookAuthor[] $bookAuthors
 */
class Book extends ActiveRecord
{
    public $authorIds = [];
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'image'], 'default', 'value' => null],
            [['title', 'year', 'isbn'], 'required'],
            [['year'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'image'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'unique'],
            [['authorIds'], 'safe'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 5],
            [['imageFile'], 'image', 'minWidth' => 100, 'minHeight' => 150, 'maxWidth' => 2000, 'maxHeight' => 2000],
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
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'image' => 'Фото обложки',
            'imageFile' => 'Фото обложки',
            'authorIds' => 'Авторы',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    /**
     * Найдем авторов
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->authorIds = ArrayHelper::getColumn($this->authors, 'id');
    }

    /**
     * Возьмем авторов в строке
     */
    public function getAuthorsString()
    {
        $authors = [];
        foreach ($this->authors as $author) {
            $authors[] = $author->getFullName();
        }
        return implode(', ', $authors);
    }

    /**
     * Загружаем изображения
     *
     * @return bool
     */
    public function upload()
    {
        if (!$this->imageFile) {
            return false;
        }

        if ($this->imageFile->error !== UPLOAD_ERR_OK) {
            $this->addError('imageFile', 'Ошибка загрузки' . $this->imageFile->error);

            return false;
        }

        $fileName = md5(uniqid() . time() . $this->id) . '.' . $this->imageFile->extension;
        $filePath = $this->getUploadPath() . $fileName;
        if ($this->imageFile->saveAs($filePath)) {
            $this->deleteOldImage();
            $this->image = $fileName;

            return true;
        }

        $this->addError('imageFile', 'Не удалось сохранить файл');
        return false;
    }

    /**
     * Получаем пути загрузки файлов
     *
     * @return string
     */
    public function getUploadPath()
    {
        $path = \Yii::getAlias('@webroot/uploads/books/');

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    /**
     * Получаем URL для доступа к изображению
     *
     * @return string|null
     */
    public function getImageUrl()
    {
        if ($this->image) {
            return \Yii::getAlias('@web/uploads/books/') . $this->image;
        }

        return null;
    }

    /**
     * Удаляем старое изображения
     */
    protected function deleteOldImage()
    {
        if ($this->getOldAttribute('image')) {
            $oldFile = $this->getUploadPath() . $this->getOldAttribute('image');
            if (file_exists($oldFile) && is_file($oldFile)) {
                unlink($oldFile);
            }
        }
    }

    /**
     * Перед удалением записи удаляем изображение
     *
     * @return bool
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        $this->deleteOldImage();

        return true;
    }

    /**
     * Сохраняем связи авторов
     */
    public function saveAuthors()
    {
        BookAuthor::deleteAll(['book_id' => $this->id]);

        if (!empty($this->authorIds)) {
            foreach ($this->authorIds as $authorId) {
                $bookAuthor = new BookAuthor();
                $bookAuthor->book_id = $this->id;
                $bookAuthor->author_id = $authorId;
                $bookAuthor->save();
            }
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!empty($this->authorIds)) {
            $this->saveAuthors();
        }
    }
}