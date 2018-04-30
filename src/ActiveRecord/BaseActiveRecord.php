<?php

namespace Cryptomkt\Exchange\ActiveRecord;

trait BaseActiveRecord
{
    private function getClient()
    {
        return ActiveRecordContext::getClient();
    }
}
