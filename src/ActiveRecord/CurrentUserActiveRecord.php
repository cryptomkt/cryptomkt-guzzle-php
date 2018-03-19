<?php

namespace Cryptomkt\Exchange\ActiveRecord;

trait CurrentUserActiveRecord 
{
    use BaseActiveRecord;

    /**
     * Issues an API request to update the current user.
     */
    public function update(array $params = [])
    {
        $this->getClient()->updateCurrentUser($this, $params);
    }
}
