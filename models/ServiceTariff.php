<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service_tariff".
 * @property integer $id
 * @property integer $key
 * @property string $price_shift
 * @property string $price_overnight
 * @property boolean $show_price_overnight
 * @property string $room_category_name
 * @property string $room_category_name_short
 * @property string $turn_duration
 * @property string $overnight_start
 * @property string $overnight_finish
 * @property string $long_turn_start
 * @property string $long_turn_finish
 * @property boolean $show_overnight_start_finish
 * @property boolean $show_long_turn_start_finish
 */
class ServiceTariff extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'service_tariff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['key'], 'integer'],
            [
                [
                    'show_price_overnight',
                    'show_overnight_start_finish',
                    'show_long_turn_start_finish',
                ],
                'boolean',
            ],
            [['price_shift', 'price_overnight'], 'number'],
            [
                [
                    'room_category_name',
                    'room_category_name_short',
                    'turn_duration',
                    'overnight_start',
                    'overnight_finish',
                    'long_turn_start',
                    'long_turn_finish',
                ],
                'string',
                'max' => 255,
            ],
        ];
    }

    /**
     * Save a service tariff from one SGH service tariff data object.
     * @param object $tariff
     * @return ServiceTariff|null The saved model or null if an error occurred.
     */
    public static function saveFromSGHData($tariff) {
        $serviceTariff = new self();

        // Assign attributes.
        $serviceTariff->key = $tariff->RCKEY;
        $serviceTariff->price_shift = $tariff->TURNPRICE;
        $serviceTariff->price_overnight = $tariff->OVERNIGHTPRICE;
        $serviceTariff->show_price_overnight = $tariff->SHOWOVNPRICE;
        $serviceTariff->room_category_name = $tariff->RCNAME;
        $serviceTariff->room_category_name_short = $tariff->RCSHORTNAME;
        $serviceTariff->turn_duration = $tariff->TURNDURATION;
        $serviceTariff->overnight_start = $tariff->OVERNIGHTSTART;
        $serviceTariff->overnight_finish = $tariff->OVERNIGHTFINISH;
        $serviceTariff->long_turn_start = $tariff->LONGTURNSTART;
        $serviceTariff->long_turn_finish = $tariff->LONGTURNFINISH;
        $serviceTariff->show_overnight_start_finish =
            $tariff->SHOWOVNSTARTFINISH;
        $serviceTariff->show_long_turn_start_finish =
            $tariff->SHOWLONGSTARTFINISH;

        if ($serviceTariff->save()) {
            return $serviceTariff;
        } else {
            return null;
        }
    }
}
