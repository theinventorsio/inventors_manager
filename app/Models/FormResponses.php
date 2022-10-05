<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormResponses extends Model
{
    protected $table = 'form_response';
    protected $fillable = [
        'id_form',
        'id_response',
        'nome',
        'satisfacao',
        'faltou_material',
        'novos_inscritos',
        'num_alunos',
        'presenca_inscritos',
        'observacoes',
        'outros_convidados',
        'monitor',
        'ref_forms',
        'categorizacao',
        'alunos_concluiram',
        'ficou_material',
        'conversas_staff',
        'create_date_response',
        'status',
        'status_observation',
        'created_at',
        'updated_at'
        ];

    public $timestamps = true;
}
