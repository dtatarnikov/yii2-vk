<?php
namespace strong2much\vk\widgets;

use yii\helpers\Html;
use yii\web\View;

/**
 * This is the widget class for vk share button widget.
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class ShareButtonWidget extends Widget
{
    const TYPE_ROUND = 'round';
    const TYPE_ROUND_NOCOUNT = 'round_nocount';
    const TYPE_BUTTON = 'button';
    const TYPE_BUTTON_NOCOUNT = 'button_nocount';
    const TYPE_LINK = 'link';
    const TYPE_LINK_NOICON = 'link_noicon';
    const TYPE_CUSTOM = 'custom';

    public $url;
    public $type = self::TYPE_ROUND;
    /**
     * @var string text for button. Don't work with type 'custom'
     */
    public $text = 'Publish';
    public $htmlOptions = array();
    private $_vkWidgetId = 'vk_share';

    /**
     * Executes the widget.
     */
    public function run()
    {
        if (!$this->enabled) {
            return;
        }

        $view = $this->view;
        $view->registerJsFile('//vk.com/js/api/share.js', [
            'position' => View::POS_END,
        ]);

        if(empty($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->_vkWidgetId;
        } else {
            $this->_vkWidgetId = $this->htmlOptions['id'];
        }
        echo Html::tag('div', '', $this->htmlOptions);

        $css = <<<CSS
#{$this->_vkWidgetId} table td,
#{$this->_vkWidgetId} table th {
    padding: 0;
}
CSS;

        $view->registerCss($css);

        if($this->url!=null && $this->url!="") {
            $vkUrl = '{url: "'.$this->url.'"}';
        } else {
            $vkUrl = 'false';
        }
        if($this->type==self::TYPE_CUSTOM) {
            $text = '<img src=\"http://vk.com/images/vk32.png?1\" />';
        } else {
            $text = $this->text;
        }

        $js = <<<JS
$("#{$this->_vkWidgetId}").html(VK.Share.button({$vkUrl}, {type: "{$this->type}", text: "{$text}"}));
JS;

        $view->registerJs($js, View::POS_END);
    }

}