<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $table = "userinfo";

    public function getAuthPassword()
    {
        return $this->pword;
    }
}
