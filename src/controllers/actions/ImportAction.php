<?php

namespace dlds\translatemanager\controllers\actions;

use dlds\translatemanager\models\Language;
use dlds\translatemanager\services\Generator;
use Yii;
use yii\web\UploadedFile;
use dlds\translatemanager\models\ImportForm;
use dlds\translatemanager\bundles\LanguageAsset;
use dlds\translatemanager\bundles\LanguagePluginAsset;

/**
 * Class for exporting translations.
 */
class ImportAction extends \yii\base\Action
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        LanguageAsset::register($this->controller->view);
        LanguagePluginAsset::register($this->controller->view);
        parent::init();
    }

    /**
     * Show import form and import the uploaded file if posted
     *
     * @return string
     *
     * @throws \Exception
     */
    public function run()
    {
        $model = new ImportForm();

        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');

            if ($model->validate()) {
                try {
                    $result = $model->import();

                    $message = Yii::t('language', 'Successfully imported {fileName}', ['fileName' => $model->importFile->name]);
                    $message .= "<br/>\n";
                    foreach ($result as $type => $typeResult) {
                        $message .= "<br/>\n" . Yii::t('language', '{type}: {new} new, {updated} updated', [
                            'type' => $type,
                            'new' => $typeResult['new'],
                            'updated' => $typeResult['updated'],
                        ]);
                    }

                    $languageIds = Language::find()
                        ->select('code')
                        ->where(['status_translation' => Language::STATUS_ACTIVE])
                        ->column();

                    foreach ($languageIds as $languageId) {
                        $generator = new Generator($this->controller->module, $languageId);
                        $generator->run();
                    }

                    Yii::$app->getSession()->setFlash('success', $message);
                } catch (\Exception $e) {
                    if (YII_DEBUG) {
                        throw $e;
                    } else {
                        Yii::$app->getSession()->setFlash('danger', str_replace("\n", "<br/>\n", $e->getMessage()));
                    }
                }
            }
        }

        return $this->controller->render('import', [
            'model' => $model,
        ]);
    }
}
