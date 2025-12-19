<?php
namespace app\models;
use yii\db\ActiveRecord;

class SmsLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'sms_log';
    }

    public function rules()
    {
        return [
            [['phone', 'message'], 'required'],
            [['message', 'response'], 'string'],
            [['phone'], 'string', 'max' => 20],
            [['status'], 'string', 'max' => 20],
            [['author_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Телефон',
            'message' => 'Сообщение',
            'status' => 'Статус',
            'response' => 'Ответ API',
            'created_at' => 'Дата отправки',
            'author_id' => 'ID автора',
        ];
    }
}