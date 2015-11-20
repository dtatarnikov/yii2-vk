<?php
namespace strong2much\vk\widgets;

use yii\web\View;

/**
 * This is basic class for vkontakte widgets.
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class Widget extends \yii\base\Widget
{
    /**
     * @var bool is widget enabled
     */
    public $enabled = true;
    /**
     * @var string vk application id
     */
    public $appId;

    /**
     * @var bool is script registered
     */
    protected $_scriptRegistered = false;

    /**
     * Init
     */
    public function run()
    {
        if (!$this->enabled) {
            return;
        }

        $this->registerApi();

        parent::run();
    }

    /**
     * Register VK Open Api
     */
    public function registerApi()
    {
        if(!$this->_scriptRegistered) {
            $view = $this->view;
            $view->registerJsFile(
                '//vk.com/js/api/openapi.js',
                [
                    'async'=>'async',
                    'position' => View::POS_END,
                ]
            );

            $js = <<<JS
VK.init({apiId: {$this->appId}});
JS;

            $view->registerJs($js, View::POS_LOAD);

            $this->_scriptRegistered = true;
        }
    }
}