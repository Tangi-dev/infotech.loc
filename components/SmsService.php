<?php

namespace app\components;
use app\models\Book;
use app\models\Subscription;
use Yii;
use yii\base\Component;
use app\models\SmsLog;

class SmsService extends Component
{
    /**
     * Отправляем SMS сообщение
     */
    public function sendSms($phone, $author_id, $message)
    {
        $log = new SmsLog();
        $log->phone = $phone;
        $log->message = $message;
        $log->status = 'pending';
        $log->created_at = date('Y-m-d H:i:s');

        if (property_exists($log, 'author_id')) {
            $log->author_id = $author_id;
        } else {
            Yii::warning('В модели SmsLog нет свойства author_id');
        }

        try {
            $apiKey = 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ';
            $from = 'INFORM';
            $url = "http://smspilot.ru/api.php?send=" . urlencode($message) .
                "&to={$phone}&from={$from}&apikey={$apiKey}&format=json";
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            $log->response = json_encode($data);

            if (isset($data['send'][0]['server_id']) && $data['send'][0]['server_id'] > 0) {
                $log->status = 'sent';
                $log->save(false);

                return true;
            } else {
                $log->status = 'failed';
                $log->save(false);

                return false;
            }

        } catch (\Exception $e) {
            $log->response = $e->getMessage();
            $log->status = 'failed';
            $log->save(false);
            Yii::error('Ошибка отправки SMS: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Отправляем уведомление подписчикам о новой книге
     */
    public function sendNotifications(Book $book)
    {
        $authorIds = $book->authorIds;
        if (empty($authorIds)) {
            Yii::info('Нет авторов для книги ' . $book->id);
            return;
        }

        $subscriptions = Subscription::find()
            ->where(['author_id' => $authorIds])
            ->with('author')
            ->all();
        foreach ($subscriptions as $subscription) {
            try {
                $authorName = $subscription->author ? $subscription->author->fullName : 'Автор';
                $message = "Новая книга от {$authorName}: \"{$book->title}\" ({$book->year}).";

                if ($this->sendSms($subscription->phone, $subscription->author_id, $message)) {
                    Yii::info("SMS отправлено по номеру {$subscription->phone} о книге {$book->id}");
                } else {
                    Yii::warning("Не удалось отправить SMS по номеру {$subscription->phone} о книге {$book->id}");
                }
            } catch (\Exception $e) {
                Yii::error("Ошибка отправки SMS {$subscription->id}: " . $e->getMessage());
            }
        }
    }
}