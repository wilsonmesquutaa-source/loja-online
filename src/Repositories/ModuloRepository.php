<?php
declare(strict_types=1);
namespace App\Repositories;
use PDO;
use Throwable;
class ModuloRepository
{
    public function __construct(protected PDO $pdo) {}
    public function todos(string $sql, array $p=[]): array { $s=$this->pdo->prepare($sql); $s->execute($p); return $s->fetchAll(PDO::FETCH_ASSOC); }
    public function um(string $sql, array $p=[]): ?array { return $this->todos($sql,$p)[0]??null; }
    public function executar(string $sql, array $p=[]): int { $s=$this->pdo->prepare($sql); $s->execute($p); return $s->rowCount(); }
    // Table and field names come exclusively from application definitions.
    public function inserir(string $t, array $v): int { $this->executar('INSERT INTO '.$t.' (`'.implode('`,`',array_keys($v)).'`) VALUES ('.implode(',',array_fill(0,count($v),'?')).')',array_values($v)); return (int)$this->pdo->lastInsertId(); }
    public function atualizar(string $t,int $id,array $v): void { $this->executar('UPDATE '.$t.' SET '.implode(',',array_map(fn($k)=>'`'.$k.'`=?',array_keys($v))).' WHERE id=?',[...array_values($v),$id]); }
    public function transacao(callable $fn): mixed { $own=!$this->pdo->inTransaction(); if($own)$this->pdo->beginTransaction(); try{$v=$fn();if($own)$this->pdo->commit();return $v;}catch(Throwable $e){if($own && $this->pdo->inTransaction())$this->pdo->rollBack();throw $e;} }
    public function auditar(string $modulo,string $entidade,int $id,string $acao,mixed $antes,mixed $depois,int $admin): void { $this->inserir('modulos_auditoria',['admin_id'=>$admin?:null,'modulo'=>$modulo,'entidade'=>$entidade,'entidade_id'=>$id,'acao'=>$acao,'dados_antes_json'=>json_encode($antes,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),'dados_depois_json'=>json_encode($depois,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]); }
}
