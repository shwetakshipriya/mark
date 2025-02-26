<?php

namespace Mark\PlisioPayment\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Payment extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_plisio_payment';
        $structure->shortName = 'Mark\PlisioPayment:Payment';
        $structure->primaryKey = 'payment_id';
        $structure->columns = [
            'payment_id'      => ['type' => self::UINT, 'autoIncrement' => true],
            'user_id'         => ['type' => self::UINT],
            'coin'            => ['type' => self::STR, 'maxLength' => 50],
            'network'         => ['type' => self::STR, 'maxLength' => 50],
            'amount'          => ['type' => self::FLOAT],
            'payment_address' => ['type' => self::STR, 'maxLength' => 255],
            'qr_code'         => ['type' => self::STR, 'maxLength' => 255],
            'status'          => ['type' => self::STR, 'maxLength' => 50],
            'expires_at'      => ['type' => self::UINT],
            'created_at'      => ['type' => self::UINT]
        ];
        $structure->relations = [
            'User' => [
                'entity'     => 'XF:User',
                'type'       => self::TO_ONE,
                'conditions' => 'user_id',
                'primary'    => true
            ]
        ];
        
        return $structure;
    }
}
