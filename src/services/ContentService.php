<?php

namespace fortytwostudio\passwordprotection\services;

use yii\base\Component;
use craft\db\Query;
use Craft;

class ContentService extends Component
{
    public function getContent(): ?array
    {
        return Craft::$app->cache->getOrSet('password-content', function () {
            return (new Query())
                ->from('{{%forty_password_content}}')
                ->one() ?: null;
        });
    }
}
