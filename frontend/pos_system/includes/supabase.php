<?php
/** NebulaPOS POS - SQLite persistence adapter. */
declare(strict_types=1);
require_once __DIR__ . '/pg_connection.php';

final class SupabaseClient
{
    private PDO $pdo;
    private ?string $table;

    private const TABLE_MAP = [
        'empresas'=>'companies','usuarios'=>'users','pos_products'=>'dte_productos','pos_productos'=>'dte_productos',
        'pos_clientes'=>'mh_cliente_consumidor','clientes'=>'mh_cliente_consumidor',
        'pos_proveedores'=>'mh_proveedor_contribuyente','proveedores'=>'mh_proveedor_contribuyente',
        'pos_ventas'=>'pos_ventas','pos_sales'=>'pos_sales','pos_venta_detalles'=>'detalle_ventas','pos_sale_items'=>'detalle_ventas'
    ];

    public function __construct(?string $table=null){ $this->pdo=pg_pool(); $this->table=$this->validateTable($table); }
    public function getTableName(): ?string{return $this->table;}
    private function physicalTable():string{$this->requireTable();return self::TABLE_MAP[$this->table]??$this->table;}

    public function select(string $columns='*',array $filters=[],?int $limit=null,?int $offset=null):array{
        $table=$this->physicalTable();$this->ensureTable($table);$filters=$this->scopeFilters($table,$filters);
        $sql='SELECT '.$this->sanitizeColumns($columns).' FROM '.$this->qi($table);$params=[];$where=$this->buildFilters($filters,$params);
        if($where)$sql.=' WHERE '.$where;$sql.=' ORDER BY rowid DESC';
        if($limit!==null&&$limit>0)$sql.=' LIMIT '.(int)$limit;if($offset!==null&&$offset>=0)$sql.=' OFFSET '.(int)$offset;
        try{$stmt=$this->pdo->prepare($sql);$stmt->execute($params);return ['success'=>true,'data'=>$stmt->fetchAll(),'count'=>$stmt->rowCount()];}
        catch(Throwable $e){return ['success'=>false,'data'=>[],'error'=>$e->getMessage()];}
    }

    public function insert(array $data):array{
        $table=$this->physicalTable();$this->ensureTable($table);if(!$data)throw new InvalidArgumentException('Insert data cannot be empty');
        if($this->hasColumn($table,'company_id')&&!array_key_exists('company_id',$data)&&!empty($_SESSION['empresa_id']))$data['company_id']=(int)$_SESSION['empresa_id'];
        if($this->hasColumn($table,'empresa_id')&&!array_key_exists('empresa_id',$data)&&!empty($_SESSION['empresa_id']))$data['empresa_id']=(int)$_SESSION['empresa_id'];
        if(!array_key_exists('id',$data)&&!$this->integerPrimaryKey($table))$data['id']=$this->uuid();
        $this->ensureColumns($table,array_keys($data));$columns=array_keys($data);$params=[];
        foreach($columns as $column)$params[':v_'.$column]=$this->normalize($data[$column]);
        $sql='INSERT INTO '.$this->qi($table).' ('.implode(', ',array_map([$this,'qi'],$columns)).') VALUES ('.implode(', ',array_keys($params)).')';
        try{$stmt=$this->pdo->prepare($sql);$stmt->execute($params);$id=$this->pdo->lastInsertId();$row=[];
            if($this->hasColumn($table,'id')){$lookup=array_key_exists('id',$data)?$data['id']:$id;if($lookup!==''&&$lookup!=='0'&&$lookup!==null){$q=$this->pdo->prepare('SELECT * FROM '.$this->qi($table).' WHERE id=? LIMIT 1');$q->execute([$lookup]);$row=$q->fetch()?:[];}}
            return ['success'=>true,'data'=>$row?[$row]:[],'id'=>$id?:($data['id']??null),'count'=>1];
        }catch(Throwable $e){return ['success'=>false,'data'=>[],'error'=>$e->getMessage()];}
    }

    public function update(array $data,array $filters=[]):array{
        $table=$this->physicalTable();$this->ensureTable($table);if(!$data)return ['success'=>true,'data'=>[],'affected'=>0];
        $this->ensureColumns($table,array_keys($data));$filters=$this->scopeFilters($table,$filters);$params=[];$sets=[];
        foreach($data as $column=>$value){$p=':set_'.$column;$sets[]=$this->qi($column).'='.$p;$params[$p]=$this->normalize($value);}
        $where=$this->buildFilters($filters,$params);if(!$where)throw new RuntimeException('UPDATE requires filters');
        try{$stmt=$this->pdo->prepare('UPDATE '.$this->qi($table).' SET '.implode(', ',$sets).' WHERE '.$where);$stmt->execute($params);return ['success'=>true,'data'=>[],'affected'=>$stmt->rowCount()];}
        catch(Throwable $e){return ['success'=>false,'data'=>[],'error'=>$e->getMessage()];}
    }

    public function delete(array $filters=[]):array{
        $table=$this->physicalTable();$this->ensureTable($table);$filters=$this->scopeFilters($table,$filters);$params=[];$where=$this->buildFilters($filters,$params);
        if(!$where)throw new RuntimeException('DELETE requires filters');
        try{$stmt=$this->pdo->prepare('DELETE FROM '.$this->qi($table).' WHERE '.$where);$stmt->execute($params);return ['success'=>true,'data'=>[],'affected'=>$stmt->rowCount()];}
        catch(Throwable $e){return ['success'=>false,'data'=>[],'error'=>$e->getMessage()];}
    }

    public function query(string $sql):array{
        if(preg_match('/\b(jsonb|bigserial|uuid_generate|FOR\s+UPDATE|ILIKE|RETURNING\s+\*)\b/i',$sql))return ['success'=>false,'data'=>[],'error'=>'Consulta PostgreSQL no compatible con SQLite'];
        try{$stmt=$this->pdo->query($sql);return ['success'=>true,'data'=>$stmt->fetchAll()];}catch(Throwable $e){return ['success'=>false,'data'=>[],'error'=>$e->getMessage()];}
    }
    public function insertVenta(array $ventaData):array{$old=$this->table;$this->table='pos_ventas';try{return $this->insert($ventaData);}finally{$this->table=$old;}}
    public function insertDTE(array $dteData,?string $usuarioId=null,?string $empresaNit=null):array{
        $codigo=$dteData['identificacion']['codigoGeneracion']??null;if(!$codigo)return ['success'=>false,'error'=>'Código de generación es requerido'];$old=$this->table;$this->table='dte_facturas';
        try{return $this->insert(['codigo_generacion'=>$codigo,'emisor_nit'=>$empresaNit??($dteData['emisor']['nit']??null),'numero_control'=>$dteData['identificacion']['numeroControl']??null,'tipo_dte'=>$dteData['identificacion']['tipoDte']??'01','fecha_emision'=>($dteData['identificacion']['fecEmi']??date('Y-m-d')).' '.($dteData['identificacion']['horEmi']??date('H:i:s')),'total_pagar'=>$dteData['resumen']['totalPagar']??0,'documento_json'=>json_encode($dteData,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),'estado_firma'=>'pendiente','origen'=>'interno','usuario_id'=>$usuarioId,'metadata'=>json_encode(['empresa_nit'=>$empresaNit],JSON_UNESCAPED_UNICODE)]);}finally{$this->table=$old;}
    }
    public function rpc(string $functionName,array $params=[]):array{return ['success'=>false,'data'=>[],'error'=>'RPC externo eliminado: '.$functionName];}

    private function scopeFilters(string $table,array $filters):array{
        if(empty($_SESSION['pos_authenticated'])||empty($_SESSION['empresa_id']))return $filters;
        $protected=!in_array($table,['users','companies'],true);
        if(!$protected)return $filters;
        if($this->hasColumn($table,'company_id')&&!array_key_exists('company_id',$filters))$filters['company_id']=(int)$_SESSION['empresa_id'];
        elseif($this->hasColumn($table,'empresa_id')&&!array_key_exists('empresa_id',$filters))$filters['empresa_id']=(int)$_SESSION['empresa_id'];
        return $filters;
    }
    private function ensureTable(string $table):void{if(!$this->tableExists($table))$this->pdo->exec('CREATE TABLE '.$this->qi($table).' (id INTEGER PRIMARY KEY AUTOINCREMENT)');}
    private function ensureColumns(string $table,array $columns):void{foreach($columns as $column){if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/',(string)$column))throw new InvalidArgumentException('Invalid column: '.$column);if(!$this->hasColumn($table,(string)$column))$this->pdo->exec('ALTER TABLE '.$this->qi($table).' ADD COLUMN '.$this->qi((string)$column).' TEXT');}}
    private function hasColumn(string $table,string $column):bool{$stmt=$this->pdo->query('PRAGMA table_info('.$this->qi($table).')');foreach($stmt->fetchAll() as $row)if($row['name']===$column)return true;return false;}
    private function integerPrimaryKey(string $table):bool{$stmt=$this->pdo->query('PRAGMA table_info('.$this->qi($table).')');foreach($stmt->fetchAll() as $row)if($row['name']==='id')return strtoupper((string)$row['type'])==='INTEGER'&&(int)$row['pk']===1;return false;}
    private function tableExists(string $table):bool{$stmt=$this->pdo->prepare("SELECT 1 FROM sqlite_master WHERE type='table' AND name=? LIMIT 1");$stmt->execute([$table]);return(bool)$stmt->fetchColumn();}
    private function buildFilters(array $filters,array &$params):string{$where=[];$i=0;foreach($filters as $column=>$value){if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/',(string)$column))continue;if(is_array($value)){$ph=[];foreach($value as $v){$p=':filter_'.$i++;$ph[]=$p;$params[$p]=$this->normalize($v);}if($ph)$where[]=$this->qi((string)$column).' IN ('.implode(',',$ph).')';}else{$p=':filter_'.$i++;$where[]=$this->qi((string)$column).'='.$p;$params[$p]=$this->normalize($value);}}return implode(' AND ',$where);}
    private function sanitizeColumns(string $columns):string{if($columns==='*')return '*';foreach(array_map('trim',explode(',',$columns))as $part)if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?(\s+AS\s+[A-Za-z_][A-Za-z0-9_]*)?$/i',$part))throw new InvalidArgumentException('Invalid column expression');return $columns;}
    private function validateTable(?string $table):?string{if($table===null||$table==='')return null;if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/',$table))throw new InvalidArgumentException('Invalid table name');return $table;}
    private function requireTable():void{if(!$this->table)throw new RuntimeException('Table name is not set');}
    private function normalize($value){if(is_bool($value))return$value?1:0;if(is_array($value)||is_object($value))return json_encode($value,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);return$value;}
    private function uuid():string{return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',random_int(0,0xffff),random_int(0,0xffff),random_int(0,0xffff),random_int(0,0x0fff)|0x4000,random_int(0,0x3fff)|0x8000,random_int(0,0xffff),random_int(0,0xffff),random_int(0,0xffff));}
    private function qi(string $identifier):string{if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/',$identifier))throw new InvalidArgumentException('Invalid identifier');return '"'.$identifier.'"';}
}
function supabase(?string $table=null):SupabaseClient{static$instances=[];$key=$table??'__default__';if(!isset($instances[$key]))$instances[$key]=new SupabaseClient($table);return$instances[$key];}
