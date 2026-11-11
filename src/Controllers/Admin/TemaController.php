<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;

use App\Helpers\Csrf;

use App\Helpers\Decimal as D;

use App\Repositories\ModuloRepository;

use App\Services\TemaAssetService;

use App\Services\TemaResolverService;

use App\Services\TemaCalendarioService;

use DomainException;

use Throwable;

final class TemaController extends Controller

{

    public const CORES = [
        'cor_primaria',
        'cor_secundaria',
        'cor_destaque',
        'cor_fundo',
        'cor_texto',
        'cor_header_inicio',
        'cor_header_fim',
        'cor_footer',
    ];

    public const EFEITOS_HEADER = [
        'header_luzes',
        'header_guirlanda',
        'header_bandeirinhas',
        'header_coracoes',
        'header_flores',
        'header_serpentina',
    ];

    public const EFEITOS_GLOBAIS = [
        'efeito_neve',
        'efeito_confete',
        'efeito_fogos',
        'efeito_baloes',
    ];

    public const EFEITOS = [
        ...self::EFEITOS_HEADER,
        ...self::EFEITOS_GLOBAIS,
        'estrelas',
    ];

    private function proteger(bool $post=false): void

    {

        if(empty($_SESSION['usuario_admin']['id']))$this->redirecionar('/login-admin');

        header('Cache-Control: private, no-store');header('X-Robots-Tag: noindex, nofollow');

        if($post&&!Csrf::validar(is_string($_POST['_token']??null)?$_POST['_token']:null)){http_response_code(403);exit('Sessão inválida.');}

    }

    public function index(): void {$this->tela('temas');}

    public function editar(int $id): void {$this->tela('editar',$id);}

    public function calendario(): void {$this->tela('calendario');}

    public function biblioteca(): void {$this->tela('biblioteca');}

    public function historico(): void {$this->tela('historico');}

    private function tela(string $secao,int $id=0): void

    {

        $this->proteger();$r=new ModuloRepository($this->pdo);

        $mostrarExcluidos = $secao === 'temas' && ($_GET['excluidos'] ?? '') === '1';
        $temas=$r->todos("SELECT * FROM temas_loja WHERE status " . ($mostrarExcluidos ? "='arquivado'" : "<>'arquivado'") . ' ORDER BY tipo,prioridade DESC,id');

        $tema=$id?$r->um('SELECT * FROM temas_loja WHERE id=?',[$id]):null;

        if($id&&!$tema){http_response_code(404);exit('Tema não encontrado.');}

        $paleta=$id?$r->um('SELECT * FROM tema_paletas WHERE tema_id=?',[$id]):[];

        $assets=$id?$r->todos('SELECT * FROM tema_assets WHERE tema_id=? ORDER BY ordem,id',[$id]):[];

        $componentes=$id?array_column($r->todos('SELECT * FROM tema_componentes WHERE tema_id=?',[$id]),null,'componente'):[];

        $config=$id?array_column($r->todos('SELECT * FROM tema_configuracoes WHERE tema_id=?',[$id]),'valor','chave'):[];

        $agendas=$r->todos('SELECT a.*,t.nome FROM tema_agendamentos a JOIN temas_loja t ON t.id=a.tema_id ORDER BY a.id DESC');

        $biblioteca=$r->todos('SELECT * FROM biblioteca_visual WHERE ativo=1 ORDER BY id DESC LIMIT 200');
        $usos=$r->todos(
            'SELECT a.url_arquivo,t.nome,a.tipo
             FROM tema_assets a
             JOIN temas_loja t ON t.id=a.tema_id
             WHERE a.ativo=1
             ORDER BY a.url_arquivo,t.nome,a.id'
        );
        $rotulos=self::rotulosAsset();
        $usosPorUrl=[];
        foreach($usos as $uso){
            $usosPorUrl[$uso['url_arquivo']][]=[
                'tema'=>$uso['nome']??'Tema desconhecido',
                'tipo'=>$rotulos[$uso['tipo']]??$uso['tipo'],
            ];
        }
        foreach($biblioteca as &$imagem){
            $imagem['usos']=$usosPorUrl[$imagem['url_arquivo']]??[];
            $imagem['em_uso']=(bool)$imagem['usos'];
        }
        unset($imagem);

        $historico=$secao==='historico'?$r->todos('SELECT p.*,t.nome FROM tema_publicacoes p JOIN temas_loja t ON t.id=p.tema_id ORDER BY p.id DESC LIMIT 200'):[];

        $auditoria=$secao==='historico'?$r->todos("SELECT * FROM modulos_auditoria WHERE modulo='temas' ORDER BY id DESC LIMIT 200"):[];

        $atual=(new TemaResolverService($this->pdo))->resolver();

        $this->view('admin/temas/index',compact('secao','temas','tema','paleta','assets','componentes','config','agendas','biblioteca','historico','auditoria','atual'));

    }

    public function preview(int $id): void

    {

        $this->proteger();$r=new ModuloRepository($this->pdo);

        if(!$r->um("SELECT id FROM temas_loja WHERE id=? AND status<>'arquivado'",[$id])){http_response_code(404);exit('Tema não encontrado.');}

        $GLOBALS['temaPreviewId']=$id;

        (new \App\Controllers\Site\HomeController($this->pdo))->index();

    }

    private function publicarLog(ModuloRepository $r,int $id,string $acao,int $admin,string $detalhes): void

    { $r->inserir('tema_publicacoes',['tema_id'=>$id,'acao'=>$acao,'admin_id'=>$admin,'origem'=>'manual','detalhes'=>$detalhes]); }

    public function acao(): void

    {

        $this->proteger(true);$r=new ModuloRepository($this->pdo);$admin=(int)$_SESSION['usuario_admin']['id'];$id=(int)($_POST['tema_id']??0);$acao=(string)($_POST['acao']??'');$destino='/admin/temas';

        $arquivosFisicosParaExcluir = [];
        $resultadoExclusao = null;
        try{
            $r->transacao(function()use($r,$admin,&$id,$acao,&$destino,&$arquivosFisicosParaExcluir,&$resultadoExclusao){

                $tema=$id?$r->um('SELECT * FROM temas_loja WHERE id=? FOR UPDATE',[$id]):null;

                if($id&&!$tema)throw new DomainException('Tema inexistente.');

                if($acao==='salvar'){

                    if($tema&&($tema['status']!=='rascunho'||$tema['tipo']==='padrao'))throw new DomainException('Use Editar novamente para reabrir este tema como rascunho.');

                    $nome=trim((string)($_POST['nome']??''));$slug=trim((string)($_POST['slug']??''));

                    if($nome===''||mb_strlen($nome)>120||!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$slug)||strlen($slug)>140)throw new DomainException('Nome ou identificador inválido.');

                    $v=['nome'=>$nome,'slug'=>$slug,'descricao'=>mb_substr((string)($_POST['descricao']??''),0,500),'tipo'=>($_POST['tipo']??'')==='campanha'?'campanha':'sazonal','prioridade'=>D::valor($_POST['prioridade']??100,'0','65535',0),'permitir_agendamento'=>isset($_POST['permitir_agendamento'])?1:0];

                    if($id)$r->atualizar('temas_loja',$id,$v);else $id=$r->inserir('temas_loja',$v+['criado_por_admin_id'=>$admin]);

                    $cores=['tema_id'=>$id];

                    foreach(self::CORES as $k){$c=trim((string)($_POST[$k]??''));if($c!==''&&!preg_match('/^#[0-9a-fA-F]{6}$/D',$c))throw new DomainException('Cor inválida. Use #RRGGBB.');$cores[$k]=$c?:null;}

                    // Contraste mínimo entre texto e fundo explicitamente definidos.

                    if($cores['cor_texto']&&$cores['cor_fundo']){

                        $lum=static function($hex){$v=[];foreach(str_split(substr($hex,1),2) as $part){$c=hexdec($part)/255;$v[]=$c<=.04045?$c/12.92:pow(($c+.055)/1.055,2.4);}return $v[0]*.2126+$v[1]*.7152+$v[2]*.0722;};

                        $a=$lum($cores['cor_texto']);$b=$lum($cores['cor_fundo']);if((max($a,$b)+.05)/(min($a,$b)+.05)<4.5)throw new DomainException('Aumente o contraste entre texto e fundo (mínimo 4,5:1).');

                    }

                    $old=$r->um('SELECT id FROM tema_paletas WHERE tema_id=?',[$id]);if($old)$r->atualizar('tema_paletas',(int)$old['id'],$cores);else $r->inserir('tema_paletas',$cores);

                    $r->executar('DELETE FROM tema_componentes WHERE tema_id=?',[$id]);

                    foreach(self::EFEITOS as $ef)$r->inserir('tema_componentes',['tema_id'=>$id,'componente'=>$ef,'ativo'=>isset($_POST['efeitos'][$ef])?1:0]);

                    foreach(['selo','borda','sombra'] as $chave){$valor=mb_substr(trim((string)($_POST[$chave]??'')),0,80);$r->executar("INSERT INTO tema_configuracoes(tema_id,chave,valor,tipo) VALUES (?,?,?,'texto') ON DUPLICATE KEY UPDATE valor=VALUES(valor)",[$id,$chave,$valor]);}

                    $r->auditar('temas','temas_loja',$id,'salvo',$tema,['tema'=>$v,'paleta'=>$cores,'efeitos'=>$_POST['efeitos']??[]],$admin);

                    $destino.='/editar/'.$id;
                    if(($_POST['apos_salvar']??'')==='preview')$destino='/admin/temas/preview/'.$id;
                    if(($_POST['apos_salvar']??'')==='aplicar'){
                        (new \App\Services\TemaPublicacaoService($this->pdo))->publicarAtivar($id,$admin);
                    }

                }elseif($acao==='publicar_ativar'){
                    (new \App\Services\TemaPublicacaoService($this->pdo))->publicarAtivar($id,$admin);
                    $destino.='/editar/'.$id;
                }elseif(in_array($acao,['reabrir','excluir'],true)){
                    (new \App\Services\TemaEdicaoService($this->pdo))->alterarEstado($id,$acao,$admin);
                    if($acao==='reabrir')$destino.='/editar/'.$id;
                }elseif($acao==='duplicar'){

                    if(!$tema)throw new DomainException('Selecione um tema.');

                    $novo=$r->inserir('temas_loja',['nome'=>mb_substr($tema['nome'].' (cópia)',0,120),'slug'=>mb_substr($tema['slug'],0,120).'-'.bin2hex(random_bytes(4)),'tipo'=>$tema['tipo']==='padrao'?'campanha':$tema['tipo'],'prioridade'=>$tema['prioridade'],'criado_por_admin_id'=>$admin]);

                    foreach(['tema_paletas','tema_assets','tema_componentes','tema_configuracoes'] as $t)foreach($r->todos('SELECT * FROM '.$t.' WHERE tema_id=?',[$id]) as $v){unset($v['id'],$v['criado_em'],$v['atualizado_em']);$v['tema_id']=$novo;$r->inserir($t,$v);}

                    $r->auditar('temas','temas_loja',$novo,'duplicado',$tema,['origem'=>$id],$admin);$destino.='/editar/'.$novo;

                }elseif(in_array($acao,['publicar','ativar','desativar','padrao','arquivar'],true)){

                    // Serializa trocas manuais para nunca haver duas ativações simultâneas.

                    $r->um("SELECT id FROM temas_loja WHERE tipo='padrao' FOR UPDATE");

                    if($acao==='padrao'){

                        $r->executar('UPDATE temas_loja SET ativo_manual=0');

                        $padrao=$r->um("SELECT id FROM temas_loja WHERE tipo='padrao'");

                        $r->atualizar('temas_loja',(int)$padrao['id'],['ativo_manual'=>1]);

                        $this->publicarLog($r,(int)$padrao['id'],'restaurado',$admin,'Padrão fixado manualmente; agendamentos preservados.');

                    }else{

                        if(!$tema)throw new DomainException('Tema inexistente.');

                        if($tema['tipo']==='padrao'&&in_array($acao,['arquivar','publicar'],true))throw new DomainException('O tema padrão é permanente.');

                        if(in_array($acao,['publicar','ativar'],true)){

                            foreach($r->todos('SELECT * FROM tema_assets WHERE tema_id=? AND ativo=1',[$id]) as $a)if(!TemaAssetService::existente($a['url_arquivo']))throw new DomainException('Há arquivo ausente ou inválido no tema.');

                            if($acao==='ativar'&&$tema['status']!=='publicado')throw new DomainException('Publique o tema antes de ativar.');

                        }

                        if($acao==='ativar')$r->executar('UPDATE temas_loja SET ativo_manual=0');

                        $v=match($acao){'publicar'=>['status'=>'publicado'],'ativar'=>['ativo_manual'=>1],'desativar'=>['ativo_manual'=>0],'arquivar'=>['status'=>'arquivado','ativo_manual'=>0]};

                        $r->atualizar('temas_loja',$id,$v);

                        $this->publicarLog($r,$id,match($acao){'publicar'=>'publicado','ativar'=>'ativado',default=>'desativado'},$admin,$acao);

                        $r->auditar('temas','temas_loja',$id,$acao,$tema,$v,$admin);

                    }

                }elseif($acao==='asset'||$acao==='upload'){

                    if($acao==='asset'&&(!$tema||$tema['status']!=='rascunho'))throw new DomainException('Edite os arquivos somente em um rascunho.');

                    $tipoUpload = $acao === 'asset' ? (string)($_POST['tipo_asset'] ?? '') : '';
                    $biblioteca=(int)($_POST['biblioteca_id']??0);

                    if($biblioteca){$arquivo=$r->um('SELECT * FROM biblioteca_visual WHERE id=? AND ativo=1',[$biblioteca]);if(!$arquivo||!TemaAssetService::existente($arquivo['url_arquivo']))throw new DomainException('Arquivo inválido.');}

                    else{$arquivo=TemaAssetService::upload($_FILES['arquivo']??[], $tipoUpload);$r->inserir('biblioteca_visual',$arquivo+['nome'=>mb_substr(trim((string)($_POST['nome_arquivo']??'Imagem')),0,150)?:'Imagem','categoria'=>'temas']);}

                    if($acao==='asset'){

                        $tipo=(string)($_POST['tipo_asset']??'');if(!in_array($tipo,TemaAssetService::TIPOS,true))throw new DomainException('Tipo inválido.');

                        if ($tipo !== 'mascote' && str_ends_with($arquivo['url_arquivo'], '.webm')) throw new DomainException('Vídeo WebM só pode ser usado como mascote.');
                        if ($tipo !== 'mascote' && TemaAssetService::webpAnimado(APP_ROOT . '/public' . $arquivo['url_arquivo'])) {
                            throw new DomainException('Esta imagem animada só pode ser usada como mascote.');
                        }

                        $v=['tema_id'=>$id,'tipo'=>$tipo,'url_arquivo'=>$arquivo['url_arquivo'],'texto_alternativo'=>mb_substr((string)($_POST['texto_alternativo']??''),0,255),'posicao_x'=>D::valor($_POST['posicao_x']??50,'0','100',2),'posicao_y'=>D::valor($_POST['posicao_y']??50,'0','100',2),'escala'=>D::valor($_POST['escala']??1,'0.1','2',2),'opacidade'=>D::valor($_POST['opacidade']??1,'0','1',2)];

                        $r->executar('UPDATE tema_assets SET ativo=0 WHERE tema_id=? AND tipo=?',[$id,$tipo]);$aid=$r->inserir('tema_assets',$v);$r->auditar('temas','tema_assets',$aid,'adicionado',null,$v,$admin);$destino.='/editar/'.$id;

                    }else $destino.='/biblioteca';

                }elseif($acao==='editar_asset'){
                    if (!$tema || $tema['status'] !== 'rascunho') throw new DomainException('Edite apenas rascunhos.');
                    $a = $r->um('SELECT * FROM tema_assets WHERE id=? AND tema_id=? AND ativo=1 FOR UPDATE', [(int)($_POST['asset_id'] ?? 0), $id]);
                    if (!$a) throw new DomainException('Imagem não encontrada neste tema.');
                    $v = [
                        'texto_alternativo' => mb_substr(trim((string)($_POST['texto_alternativo'] ?? '')), 0, 255),
                        'posicao_x' => D::valor($_POST['posicao_x'] ?? $a['posicao_x'], '0', '100', 2),
                        'posicao_y' => D::valor($_POST['posicao_y'] ?? $a['posicao_y'], '0', '100', 2),
                        'escala' => D::valor($_POST['escala'] ?? $a['escala'], '0.1', '2', 2),
                        'opacidade' => D::valor($_POST['opacidade'] ?? $a['opacidade'], '0', '1', 2),
                    ];
                    $r->atualizar('tema_assets', (int)$a['id'], $v);
                    $r->auditar('temas', 'tema_assets', (int)$a['id'], 'editado', $a, $v, $admin);
                    $destino .= '/editar/'.$id;

                }elseif($acao==='remover_asset'){

                    if(!$tema||$tema['status']!=='rascunho')throw new DomainException('Edite apenas rascunhos.');

                    $a=$r->um('SELECT * FROM tema_assets WHERE id=? AND tema_id=?',[(int)($_POST['asset_id']??0),$id]);if(!$a)throw new DomainException('Arquivo não encontrado.');

                    $r->atualizar('tema_assets',(int)$a['id'],['ativo'=>0]);$r->auditar('temas','tema_assets',(int)$a['id'],'desativado',$a,['ativo'=>0],$admin);$destino.='/editar/'.$id;

                }elseif(in_array($acao,['excluir_biblioteca','excluir_biblioteca_lote'],true)){

                    $ids=$acao==='excluir_biblioteca_lote'?($_POST['biblioteca_ids']??[]):[$_POST['biblioteca_id']??0];
                    if(!is_array($ids)||count($ids)<1||count($ids)>200)throw new DomainException('Seleção de imagens inválida.');
                    $ids=array_values(array_unique(array_filter(array_map('intval',$ids),static fn(int $valor): bool=>$valor>0)));
                    if(!$ids)throw new DomainException('Selecione ao menos uma imagem.');

                    $resultadoExclusao=['excluidas'=>0,'bloqueadas'=>[],'nao_encontradas'=>0];
                    foreach($ids as $bibliotecaId){
                        $imagem=$r->um('SELECT * FROM biblioteca_visual WHERE id=? AND ativo=1 FOR UPDATE',[$bibliotecaId]);
                        if(!$imagem){$resultadoExclusao['nao_encontradas']++;continue;}

                        $referencias=$this->usosBiblioteca($r,(string)$imagem['url_arquivo']);
                        if($referencias){
                            $resultadoExclusao['bloqueadas'][]=[
                                'nome'=>$imagem['nome'],
                                'usos'=>$referencias,
                            ];
                            continue;
                        }

                        $r->auditar('temas','biblioteca_visual',$bibliotecaId,'excluido',$imagem,null,$admin);
                        $r->executar('DELETE FROM biblioteca_visual WHERE id=?',[$bibliotecaId]);
                        $arquivosFisicosParaExcluir[]=(string)$imagem['url_arquivo'];
                        $resultadoExclusao['excluidas']++;
                    }
                    $destino='/admin/temas/biblioteca';

                }elseif($acao==='agendar'){

                    if(!$tema||$tema['status']!=='publicado'||$tema['tipo']==='padrao')throw new DomainException('Selecione um tema sazonal ou campanha já publicado.');

                    $tipo=(string)($_POST['tipo_regra']??'');

                    $v=['tema_id'=>$id,'tipo_regra'=>$tipo,'prioridade'=>D::valor($_POST['prioridade']??100,'0','65535',0)];

                    if($tipo==='intervalo_fixo'){

                        foreach(['inicio_datetime','fim_datetime'] as $k){$str=(string)($_POST[$k]??'');$d=\DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$str);if(!$d||$d->format('Y-m-d\TH:i')!==$str)throw new DomainException('Data e hora inválidas.');$v[$k]=$d->format('Y-m-d H:i:s');}

                        if($v['inicio_datetime']>$v['fim_datetime'])throw new DomainException('Fim deve ser posterior ao início.');

                    }elseif($tipo==='anual_fixo'){

                        foreach(['inicio','fim'] as $parte){$mes=(int)($_POST['mes_'.$parte]??0);$dia=(int)($_POST['dia_'.$parte]??0);if(!checkdate($mes,$dia,2024))throw new DomainException('Dia e mês inválidos.');$v['mes_'.$parte]=$mes;$v['dia_'.$parte]=$dia;}

                    }elseif($tipo==='regra_calendario'){

                        $v['regra_codigo']=(string)($_POST['regra_codigo']??'');TemaCalendarioService::data($v['regra_codigo'],2026);

                        $v['dias_antes']=D::valor($_POST['dias_antes']??0,'0','60',0);$v['dias_depois']=D::valor($_POST['dias_depois']??0,'0','60',0);

                    }else throw new DomainException('Tipo de agendamento inválido.');

                    $a=$r->inserir('tema_agendamentos',$v);$this->publicarLog($r,$id,'agendado',$admin,json_encode($v,JSON_UNESCAPED_UNICODE));$r->auditar('temas','tema_agendamentos',$a,'agendado',null,$v,$admin);$destino.='/calendario';

                }elseif($acao==='cancelar_agenda'){

                    $a=$r->um('SELECT * FROM tema_agendamentos WHERE id=? FOR UPDATE',[(int)($_POST['agenda_id']??0)]);if(!$a)throw new DomainException('Agenda inexistente.');

                    $r->atualizar('tema_agendamentos',(int)$a['id'],['ativo'=>0]);$this->publicarLog($r,(int)$a['tema_id'],'cancelado',$admin,'Agendamento #'.$a['id']);$r->auditar('temas','tema_agendamentos',(int)$a['id'],'cancelado',$a,['ativo'=>0],$admin);$destino.='/calendario';

                }else throw new DomainException('Ação inválida.');

            });

            if($resultadoExclusao!==null){
                foreach(array_unique($arquivosFisicosParaExcluir) as $url){
                    self::excluirArquivoBiblioteca($url,$r);
                }
                $partes=[];
                if($resultadoExclusao['excluidas'])$partes[]=$resultadoExclusao['excluidas'].' imagem(ns) excluída(s).';
                if($resultadoExclusao['bloqueadas']){
                    $detalhes=[];
                    foreach($resultadoExclusao['bloqueadas'] as $bloqueada){
                        $usos=array_map(static fn(array $uso): string=>$uso['tema'].' — '.$uso['tipo'],$bloqueada['usos']);
                        $detalhes[]=$bloqueada['nome'].' ('.implode('; ',$usos).')';
                    }
                    $partes[]='Não excluídas por estarem em uso: '.implode('; ',$detalhes).'.';
                }
                if($resultadoExclusao['nao_encontradas'])$partes[]=$resultadoExclusao['nao_encontradas'].' imagem(ns) não encontrada(s).';
                $_SESSION['modulos_aviso']=implode(' ',$partes)?:'Nenhuma imagem foi excluída.';
            }else{
                $_SESSION['modulos_aviso']=($acao==='publicar_ativar'||($acao==='salvar'&&($_POST['apos_salvar']??'')==='aplicar'))?'Tema salvo e aplicado à loja.':'Alteração salva com sucesso.';
            }
            TemaResolverService::invalidar();

        }catch(DomainException $e){$_SESSION['modulos_aviso']=$e->getMessage();if(in_array($acao,['excluir_biblioteca','excluir_biblioteca_lote'],true))$destino='/admin/temas/biblioteca';elseif($id)$destino='/admin/temas/editar/'.$id;}

        catch(Throwable $e){error_log('Temas: '.$e->getMessage());$_SESSION['modulos_aviso']='Não foi possível concluir a operação. Confira os dados e tente novamente.';if(in_array($acao,['excluir_biblioteca','excluir_biblioteca_lote'],true))$destino='/admin/temas/biblioteca';}

        $this->redirecionar($destino);

    }

    private static function rotulosAsset(): array
    {
        return [
            'logo_desktop'=>'Logo desktop','logo_mobile'=>'Logo mobile','mascote'=>'Mascote',
            'favicon'=>'Favicon','header_overlay'=>'Decoração do header','header_background'=>'Fundo do header',
            'banner_home'=>'Banner da home','background_site'=>'Fundo das páginas',
            'decoracao_esquerda'=>'Decoração esquerda','decoracao_direita'=>'Decoração direita',
            'decoracao_superior'=>'Decoração superior','outro'=>'Ilustração complementar',
        ];
    }

    private function usosBiblioteca(ModuloRepository $r,string $url): array
    {
        $rotulos=self::rotulosAsset();
        return array_map(
            static fn(array $uso): array=>[
                'tema'=>$uso['nome']??'Tema desconhecido',
                'tipo'=>$rotulos[$uso['tipo']]??$uso['tipo'],
            ],
            $r->todos(
                'SELECT t.nome,a.tipo FROM tema_assets a JOIN temas_loja t ON t.id=a.tema_id WHERE a.url_arquivo=? AND a.ativo=1 ORDER BY t.nome,a.id',
                [$url]
            )
        );
    }

    private static function excluirArquivoBiblioteca(string $url, ModuloRepository $r): bool
    {
        if (!preg_match('~^/assets/uploads/temas/([a-zA-Z0-9_.-]+)$~D', $url, $m)) {
            return false;
        }

        if ($r->um('SELECT id FROM biblioteca_visual WHERE url_arquivo=? LIMIT 1',[$url])
            || $r->um('SELECT id FROM tema_assets WHERE url_arquivo=? LIMIT 1',[$url])) {
            return false;
        }

        $diretorio=realpath(APP_ROOT.'/public/assets/uploads/temas');
        $arquivo=$diretorio===false?false:realpath($diretorio.DIRECTORY_SEPARATOR.$m[1]);
        if($diretorio===false||$arquivo===false||!is_file($arquivo)){
            return false;
        }

        $prefix=rtrim($diretorio,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if(strncmp($arquivo,$prefix,strlen($prefix))!==0){
            return false;
        }

        return unlink($arquivo);
    }

}
