<?php

namespace fortytwostudio\passwordprotection\controllers;

use Craft;
use craft\web\Controller;
use fortytwostudio\passwordprotection\models\Settings;
use fortytwostudio\passwordprotection\PasswordProtection;
use yii\web\Response;

class SettingsController extends Controller
{
    protected array|bool|int $allowAnonymous = [];

    /**
     * Display plugin settings.
     */
    public function actionPluginSettings(?Settings $settings = null): Response
    {
        $settings ??= PasswordProtection::getInstance()->getSettings();

        $section = Craft::$app->getRequest()->getSegment(3);

        $variables = [
            'fullPageForm' => true,
            'selectedSubnavItem' => 'settings',
            'settings' => $settings,
            'controllerHandle' => 'settings/' . $section,
            'cookieLife' => $settings->cookieLife,
            'maxLogin' => $settings->maxLogin,
            'maxLoginsPeriod' => $settings->maxLoginsPeriod,
            'includedSections' => $settings->includedSections,
            'sections' => Craft::$app->getEntries()->getEditableSections(),
        ];

        return $this->renderTemplate(
            'passwordprotection/settings/' . $section,
            $variables,
        );
    }

    /**
     * Save system settings.
     */
    public function actionSaveSystemSettings(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $plugin = PasswordProtection::getInstance();
        $settings = $plugin->getSettings();

        $settings->cookieLife = $request->getBodyParam('cookie');
        $settings->maxLogin = $request->getBodyParam('loginAttempts');
        $settings->maxLoginsPeriod = $request->getBodyParam(
            'loginAttemptsPeriod',
        );

        if (!$this->saveSettings($settings)) {
            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setNotice(
            Craft::t('app', 'Plugin settings saved.'),
        );

        return $this->redirectToPostedUrl();
    }

    /**
     * Save section settings.
     */
    public function actionSaveSectionSettings(): Response
    {
        $this->requirePostRequest();

        $plugin = PasswordProtection::getInstance();
        $settings = $plugin->getSettings();
        $includedSections = [];

        foreach (Craft::$app->getRequest()->getBodyParams() as $key => $value) {
            if ($value === '1') {
                $includedSections[] = $key;
            }
        }

        // This must be saved even when empty, so users can uncheck every section.
        $settings->includedSections = $includedSections;

        if (!$this->saveSettings($settings)) {
            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setNotice(
            Craft::t('app', 'Section settings saved.'),
        );

        return $this->redirectToPostedUrl();
    }

    /**
     * Install the login templates.
     */
    public function actionInstallTemplates(): Response
    {
        $this->requirePostRequest();

        $templatesFolder = Craft::getAlias('@templates') . '/protected-page';
        $sourceFolder = Craft::getAlias('@passwordprotection')
            . '/templates/protected-page';

        try {
            PasswordProtection::getInstance()
                ->copyTemplates
                ->copyDirectory($sourceFolder, $templatesFolder);
        } catch (\Throwable $exception) {
            Craft::$app->getSession()->setError(
                $exception->getMessage(),
            );

            return $this->redirect(
                'passwordprotection/settings/templates',
            );
        }

        Craft::$app->getSession()->setNotice(
            Craft::t(
                'app',
                'Login templates have been installed successfully.',
            ),
        );

        return $this->redirect(
            'passwordprotection/settings/templates',
        );
    }

    /**
     * Save the current settings model to project config.
     */
    private function saveSettings(Settings $settings): bool
    {
        $plugin = PasswordProtection::getInstance();

        if (
            Craft::$app->getPlugins()->savePluginSettings(
                $plugin,
                $settings->toArray(),
            )
        ) {
            return true;
        }

        Craft::$app->getSession()->setError(
            Craft::t('app', 'Couldn’t save plugin settings.'),
        );

        return false;
    }
}
