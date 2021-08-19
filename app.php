<?php
    class Dashboard{
        public $data_inicio;
        public $data_fim;
        public $total_vendas;
        public $numero_vendas;
        public $clientes_ativos;
        public $clientes_inativos;
        public $total_despesas;
        public $tipo_contato;

        public function __get($attr){
            return $this->$attr;
        }

        public function __set($attr, $value){
            $this->$attr = $value;
            return $this;
        }
    } 

    class Conexao{
        private $host = 'localhost';
        private $db_name = 'dashboard';
        private $user = 'root';
        private $password = '';

        public function conectar(){
            try{
                $conexao = new PDO(
                    "mysql:host=$this->host;dbname=$this->db_name",
                    "$this->user",
                    "$this->password"
                );

                $conexao->exec('set charset utf8');

                return $conexao;

            }catch(PDOException $e){
                echo 'Error: '.$e->getMessage();
            }
        }
    }

    class Contato{
        public $reclamacoes;
        public $elogios;
        public $sugestoes;

        public function __get($attr){
            return $this->$attr;
        }

        public function __set($attr, $value){
            $this->$attr = $value;
            return $this;
        }
    }

    class Db{
        private $conexao;
        private $dashboard;

        public function __construct(Dashboard $dashboard, Conexao $conexao){
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function getNumeroVendas() {
            $query = 'select count(*) as numero_vendas from tb_vendas where data_venda BETWEEN :data_inicio AND :data_fim';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
        }

        public function getTotalVendas() {
            $query = 'select SUM(total) as total_vendas from tb_vendas where data_venda BETWEEN :data_inicio AND :data_fim';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
        }

        public function getTotalDespesas() {
            $query = 'select SUM(total) as total_despesas from tb_despesas where data_despesa BETWEEN :data_inicio AND :data_fim';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
        }

        public function getTotalClientesAtivos() {
            $query = 'select COUNT(*) as clientes_ativos from tb_clientes where cliente_ativo = 1';
            $stmt = $this->conexao->prepare($query);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
        }

        public function getTotalClientesInativos() {
            $query = 'select COUNT(*) as clientes_inativos from tb_clientes where cliente_ativo = 0';
            $stmt = $this->conexao->prepare($query);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
        }

        public function getContato() {
            $contato = new Contato();
            //reclamacoes
            $query = 'select COUNT(*) as reclamacoes from tb_contatos where tipo_contato = 1';
            $stmt = $this->conexao->prepare($query);

            $stmt->execute();

            $contato->__set('reclamacoes', $stmt->fetch(PDO::FETCH_OBJ)->reclamacoes);

            //Elogios

            $query = 'select COUNT(*) as elogios from tb_contatos where tipo_contato = 2';
            $stmt = $this->conexao->prepare($query);

            $stmt->execute();

            $contato->__set('elogios', $stmt->fetch(PDO::FETCH_OBJ)->elogios);

            //Sugestoes

            $query = 'select COUNT(*) as sugestoes from tb_contatos where tipo_contato = 3';
            $stmt = $this->conexao->prepare($query);

            $stmt->execute();

            $contato->__set('sugestoes', $stmt->fetch(PDO::FETCH_OBJ)->sugestoes);

            return $contato;
        }
    }

    $dashboard = new Dashboard();
    $conexao = new Conexao();

    $competencia = explode("-", $_GET["competencia"]);

    $ano = $competencia[0];
    $mes = $competencia[1];

    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
    $dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$days_in_month);

    $db = new Db($dashboard, $conexao);

    $dashboard->__set('numero_vendas', $db->getNumeroVendas());
    $dashboard->__set('total_vendas', $db->getTotalVendas());
    $dashboard->__set('total_despesas', $db->getTotalDespesas());
    $dashboard->__set('clientes_ativos', $db->getTotalClientesAtivos());
    $dashboard->__set('clientes_inativos', $db->getTotalClientesInativos());
    $dashboard->__set('tipo_contato', $db->getContato());

    echo json_encode($dashboard);
    

?>