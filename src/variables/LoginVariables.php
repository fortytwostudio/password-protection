<?php

namespace fortytwostudio\passwordprotection\variables;

use fortytwostudio\passwordprotection\PasswordProtection;

use Craft;
use craft\web\View;

use yii\di\ServiceLocator;

class LoginVariables extends ServiceLocator
{
    public function field(string $fieldType, array $fieldOptions) : string
    {
        $oldMode = Craft::$app->view->getTemplateMode();
        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
        $html = Craft::$app->view->renderTemplateMacro('_includes/forms', $fieldType, [$fieldOptions]);
        Craft::$app->view->setTemplateMode($oldMode);

        return $html;
    }
}
