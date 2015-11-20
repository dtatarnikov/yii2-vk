<?php
namespace strong2much\vk\widgets;

use yii\helpers\Html;
use yii\web\View;

/**
 * This is the widget class for vk community widget.
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class CommunityWidget extends Widget
{
    const MODE_PARTICIPANTS = 0;
    const MODE_TITLE_ONLY = 1;
    const MODE_NEWS = 2;

    public $communityId;
    public $width = 200;
    public $height = 290;
    public $mode = self::MODE_PARTICIPANTS;
    /**
     * @var int Wide mode, applies only if mode (@link mode) is equal to 2
     */
    public $wideMode = false;
    public $color1;
    public $color2;
    public $color3;
    public $htmlOptions = array();
    private $_vkWidgetId = 'vk_groups';

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

        $wideMode = "";
        if($this->mode == self::MODE_NEWS && $this->wideMode) {
            $wideMode = ' wide: 1,';
        }

        $colors = '';
        if(!empty($this->color1)) {
            $colors .= ", color1: '{$this->color1}'";
        }
        if(!empty($this->color2)) {
            $colors .= ", color2: '{$this->color2}'";
        }
        if(!empty($this->color3)) {
            $colors .= ", color3: '{$this->color3}'";
        }

        echo Html::tag('div', '', $this->htmlOptions);

        $js = <<<JS
VK.Widgets.Group("{$this->_vkWidgetId}", {mode: {$this->mode}, {$wideMode} width: "{$this->width}", height: "{$this->height}"{$colors}, {$this->communityId});
JS;

        $view->registerJs($js, View::POS_LOAD);
    }

}