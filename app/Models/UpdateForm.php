<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateForm extends Model
{
    protected $table = 'update_form_request';
    protected $fillable = ['id_form_airtable', 'students', 'status', 'status_observation'];

    public $timestamps = true;
}
