<?php
declare(strict_types=1);
use App\Controllers\Admin\FinanceiroController as F;
use App\Controllers\Admin\TemaController as T;
return [
 ['method'=>'GET','path'=>'/admin/financeiro','action'=>[F::class,'index']],
 ['method'=>'GET','path'=>'/admin/financeiro/cadastros','action'=>[F::class,'cadastro']],
 ['method'=>'GET','path'=>'/admin/financeiro/compras','action'=>[F::class,'compras']],
 ['method'=>'GET','path'=>'/admin/financeiro/fichas','action'=>[F::class,'fichas']],
 ['method'=>'GET','path'=>'/admin/financeiro/rentabilidade','action'=>[F::class,'rentabilidade']],
 ['method'=>'GET','path'=>'/admin/financeiro/historico','action'=>[F::class,'historico']],
 ['method'=>'GET','path'=>'/admin/financeiro/relatorios','action'=>[F::class,'relatorios']],
 ['method'=>'POST','path'=>'/admin/financeiro/acao','action'=>[F::class,'acao']],
 ['method'=>'GET','path'=>'/admin/temas','action'=>[T::class,'index']],
 ['method'=>'GET','path'=>'/admin/temas/editar/{id}','action'=>[T::class,'editar']],
 ['method'=>'GET','path'=>'/admin/temas/preview/{id}','action'=>[T::class,'preview']],
 ['method'=>'GET','path'=>'/admin/temas/calendario','action'=>[T::class,'calendario']],
 ['method'=>'GET','path'=>'/admin/temas/biblioteca','action'=>[T::class,'biblioteca']],
 ['method'=>'GET','path'=>'/admin/temas/historico','action'=>[T::class,'historico']],
 ['method'=>'POST','path'=>'/admin/temas/acao','action'=>[T::class,'acao']],
];
