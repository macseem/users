<?php
/**
 * Created by PhpStorm.
 * User: macseem
 * Date: 1/26/15
 * Time: 11:00 PM
 */

namespace app\models;


use framework\ActiveRecord;

class Auth extends ActiveRecord{

    protected $table = 'auth';

    /**
     * @return array
     */
    public function getAttributeNames()
    {
        return array(
            'id',
            'login',
            'pass',
            'salt'
        );
    }

    /**
     * @return array
     */
    public function getRequiredFields()
    {
        return array(
            'login',
            'pass',
            'salt'
        );
    }

    /**
     * @return array ( $keyName )
     */
    public function getPrimary()
    {
        return array('id');
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
}