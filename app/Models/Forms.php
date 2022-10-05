<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forms extends Model
{
    protected $table = 'form_airtable';
    protected $fillable = ['id_form', 'id_class', 'id_last_response', 'active'];

    public $timestamps = true;
}
