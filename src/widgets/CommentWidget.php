<?php
namespace strong2much\vk\widgets;

use yii\helpers\Html;
use yii\web\View;

/**
 * This is the widget class for vk comment widget.
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class CommentWidget extends Widget
{
    public $pageId;
    public $width = 496;
    public $numPosts = 10;
    /**
     * @var bool|string Allow attach media content. If yes, then available options:
     * '*' - allow attach everything,
     * and combination of: graffiti,photo,video,audio,link
     */
    public $attachMedia = false;
    public $htmlOptions = array();
    private $_vkWidgetId = 'vk_comments';

    /**
     * Executes the widget.
     */
    public function run()
    {
        if (!$this->enabled) {
            return;
        }

        $view = $this->view;
        $view->registerJsFile('//vk.com/js/api/openapi.js', [
            'async'=>'async',
            'position' => View::POS_END,
        ]);

        if(empty($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->_vkWidgetId;
        } else {
            $this->_vkWidgetId = $this->htmlOptions['id'];
        }
        echo Html::tag('div', '', $this->htmlOptions);

        $media = ", attach: ";
        if($this->attachMedia===false) {
            $media .= 'false';
        } else {
            $media .= '"'.$this->attachMedia.'"';
        }

        $page = '';
        if(!empty($this->pageId)) {
            $page = ', "'.$this->pageId.'"';
        }

        $js = <<<JS
VK.init({apiId: {$this->appId}, onlyWidgets: true});
VK.Widgets.Comments("$this->_vkWidgetId", {limit: {$this->numPosts}, width: "{$this->width}"{$media}}{$page});
JS;

        $view->registerJs($js, View::POS_LOAD);
    }

}