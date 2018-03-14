<?php

namespace Cryptomkt\Wallet\ActiveRecord;

trait BaseActiveRecord
{
    private function getClient()
    {
        return ActiveRecordContext::getClient();
    }
}
