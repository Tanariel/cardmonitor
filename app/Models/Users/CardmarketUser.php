<?php

namespace App\Models\Users;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CardmarketUser extends Model
{
    protected $guarded = [
        'id',
    ];

    public static function updateOrCreateFromCardmarket(array $cardmarketUser) : self
    {
        $values = [
            'cardmarket_user_id' => $cardmarketUser['idUser'],
            'username' => $cardmarketUser['username'],
            'registered_at' => new Carbon($cardmarketUser['registrationDate']),

            'is_commercial' => $cardmarketUser['isCommercial'],
            'is_seller' => $cardmarketUser['isSeller'],

            'firstname' => $cardmarketUser['name']['firstName'],
            'name' => $cardmarketUser['address']['name'],
            'extra' => $cardmarketUser['address']['extra'],
            'street' => $cardmarketUser['address']['street'],
            'zip' => $cardmarketUser['address']['zip'],
            'city' => $cardmarketUser['address']['city'],
            'country' => $cardmarketUser['address']['country'],
            'phone' => $cardmarketUser['phone'],
            'email' => $cardmarketUser['email'],

            'vat' => $cardmarketUser['vat'],
            'legalinformation' => $cardmarketUser['legalInformation'],

            'risk_group' => $cardmarketUser['riskGroup'],
            'loss_percentage' => $cardmarketUser['lossPercentage'],

            'unsent_shipments' => $cardmarketUser['unsentShipments'],
            'reputation' => $cardmarketUser['reputation'],
            'ships_fast' => $cardmarketUser['shipsFast'],
            'sell_count' => $cardmarketUser['sellCount'],
            'sold_items' => $cardmarketUser['soldItems'],
            'avg_shipping_time' => $cardmarketUser['avgShippingTime'],
            'is_on_vacation' => $cardmarketUser['onVacation'],
        ];

        return self::updateOrCreate(['cardmarket_user_id' => $cardmarketUser['idUser']], $values);
    }
}
