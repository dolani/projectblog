<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    //
   

    protected $fillable = ['author_id', 'title', 'body', 'active'];


    public function user(){
    	return $this->belongsTo('App\User');
    }
}
