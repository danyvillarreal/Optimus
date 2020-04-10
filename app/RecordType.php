<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecordType extends Model
{
    /**
     * The roles that belong to the user.
     */
    public function users()
    {
        return $this->belongsTo('App\User', 'created_by_id');
    }
}
