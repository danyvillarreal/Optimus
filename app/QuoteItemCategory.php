<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteItemCategory extends Model
{
	protected $fillable = ['quote_id','category_id','descripcion'];
    //
}
