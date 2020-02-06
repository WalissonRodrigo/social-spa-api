<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App \{
    User,
    Comentario
};

class Conteudo extends Model
{
    protected $fillable = [
        'titulo', 'texto', 'imagem', 'link', 'data'
    ];

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function curtidas()
    {
        return $this->belongsToMany(User::class, 'curtidas', 'conteudo_id', 'user_id');
    }
}
