<?php
/**
 * Created by PhpStorm.
 * User: lychee
 * Date: 2018/6/9
 * Time: 17:13
 */

namespace common\widgets\country;

use backend\models\SysCountry;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class CountryWidget extends Widget
{
    public $label = '国家';
    public $colClass = 'col-md-6';


    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function run()
    {
        $country = SysCountry::find()->select(['zh_name','code'])->asArray()->all();

        $country = ArrayHelper::map($country, 'code', 'zh_name');

        return $this->render('index', [
            'colClass' => $this->colClass,
            'label' => $this->label,
            'country' => $country
        ]);
    }
}