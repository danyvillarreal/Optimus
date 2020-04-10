<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
	protected $fillable = ['document_type_id','document_number'];
    //
    
    /**
     * The roles that belong to the user.
     */
    public function users()
    {
        return $this->belongsTo('App\User', 'created_by_id');
    }
}
