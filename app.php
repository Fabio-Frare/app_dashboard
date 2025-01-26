<?php

class Dashboard {

    public $data_inicio;
    public $data_fim;
    public $numeroVendas;
    public $totalVendas;
    public $totalClientesAtivos;
    public $totalClientesInativos;
    public $totalDespesas;

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
        return $this;
    }
}

class Conexao {
    private $host   = 'localhost';
    private $dbname = 'dashboard';
    private $user   = 'root';
    private $pass   = '';

    public function conectar() {
        try {

            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->pass"
            );

            $conexao->exec('set charset utf8');

            return $conexao;

        } catch(PDOException $e) {
            echo '<p>'. $e->getMessage(). '</p>';
        }
    }
}

class Bd {
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard) {
        $this->conexao   = $conexao->conectar();
        $this->dashboard = $dashboard;
    } 

    public function getNumeroVendas() {
        $query = '
        SELECT COUNT(*) AS numero_vendas 
        FROM tb_vendas 
        WHERE data_venda BETWEEN :data_inicio AND :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim'   , $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
    }

    public function getTotalVendas() {
        $query = '
        SELECT SUM(total) AS total_vendas 
        FROM tb_vendas 
        WHERE data_venda BETWEEN :data_inicio AND :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim'   , $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }

    public function getTotalClientesAtivos() {
        $query = '
        SELECT COUNT(cliente_ativo) AS total_clientes_ativos
        FROM tb_clientes
        WHERE cliente_ativo = 1';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ)->total_clientes_ativos;
    }

    public function getTotalClientesInativos() {
        $query = '
        SELECT COUNT(cliente_ativo) AS total_clientes_inativos
        FROM tb_clientes
        WHERE cliente_ativo = 0';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_clientes_inativos;
    }
   
    
    public function getTotalDespesas() {
        $query = '
        SELECT SUM(total) as total_despesas
        FROM tb_despesas
        WHERE data_despesa BETWEEN :data_inicio AND :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim'   , $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
    }
    
}

$dashboard = new Dashboard();
$conexao   = new Conexao();

$competencia = explode('-', $_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];

$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
$dashboard->__set('data_fim'   , $ano.'-'.$mes.'-'.$dias_do_mes);

$bd = new Bd($conexao, $dashboard);
$dashboard->__set('numeroVendas'         , $bd->getNumeroVendas());
$dashboard->__set('totalVendas'          , $bd->getTotalVendas());
$dashboard->__set('totalClientesAtivos'  , $bd->getTotalClientesAtivos());
$dashboard->__set('totalClientesInativos', $bd->getTotalClientesInativos());
$dashboard->__set('totalDespesas'        , $bd->getTotalDespesas());

echo json_encode($dashboard);


?>