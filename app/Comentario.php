<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App \{
    User,
    Conteudo
};

class Comentario extends Model
{
    protected $fillable = [
        'conteudo_id', 'texto', 'data',
    ];

    public function conteudo()
    {
        return $this->belongsTo(Conteudo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
