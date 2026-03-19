<?php

namespace fortytwostudio\passwordprotection\controllers;

use Craft;
use craft\web\Controller;
use craft\db\Query;
use yii\web\Response;

class ContentController extends Controller
{

	public function actionIndex(): Response
	{
		$row = (new Query())
			->from('{{%forty_password_content}}')
			->one();

		return $this->renderTemplate('passwordprotection/content/index', [
			'contentRow' => $row,
		]);
	}

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();
        $this->requirePermission('accessCp');

        $heading = Craft::$app->getRequest()->getBodyParam('passwordHeading');
        $content = Craft::$app->getRequest()->getBodyParam('passwordContent');

        $table = '{{%forty_password_content}}';

        $row = (new \craft\db\Query())
            ->from($table)
            ->one();

        if (!$row) {
            Craft::$app->getDb()->createCommand()->insert($table, [
                'heading' => $heading,
                'content' => $content,
                'dateCreated' => new \yii\db\Expression('NOW()'),
                'dateUpdated' => new \yii\db\Expression('NOW()'),
                'uid' => \craft\helpers\StringHelper::UUID(),
            ])->execute();
        } else {
            Craft::$app->getDb()->createCommand()->update(
                $table,
                [
                    'heading' => $heading,
                    'content' => $content,
                    'dateUpdated' => new \yii\db\Expression('NOW()'),
                ],
                ['id' => $row['id']]
            )->execute();
        }

        Craft::$app->getSession()->setNotice('Content saved.');

        return $this->redirectToPostedUrl();
    }
}
