<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreateForm extends Model
{
    protected $table = 'create_form_request';
    protected $fillable = ['id_class', 'title', 'students', 'status', 'status_observation'];

    public $timestamps = true;
}
