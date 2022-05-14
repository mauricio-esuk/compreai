<?php

include_once('PostgresDao.php');
include_once('FornecedorDao.php');
include_once('validacoes/FornecedoresForm.php');

class PostgresFornecedorDao extends PostgresDao implements FornecedorDao {

    private $table_name = 'ca_fornecedor';

    public function insere() {

        if(isset($_POST['envd'])  && FornecedoresForm::validar() == "ok"){

            $query = "INSERT INTO " . $this->table_name . 
            " (fo_social, fo_fantasia, fo_cnpj, fo_ie, fo_telefone, fo_email) VALUES" .
            " (?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);

            $values = array(

                $_POST['social'], 
                $_POST['fantasia'],
                $_POST['cnpj'],
                $_POST['ie'],
                $_POST['telefone'],
                $_POST['email'] . $_POST['provedor']

            );

            if($stmt->execute($values)){

                return "Fornecedor cadastrado com sucesso";

            }else{

                return "Não foi possível cadastrar o usuário. Ocorreu um erro !";

            }

        } else if(isset($_POST['envd'])){

            return FornecedoresForm::validar();

        }

    }

    public function inserirFotos($qtd_fotos, $categoria_foto, $id, $categoria, $fotos){
        $erro = "";

        if( $qtd_fotos > 0) {

            for($i = 0; $i < $qtd_fotos; $i++){
				
				$path = 'imagens/' . $categoria . '/' . $id . '/' . $categoria_foto . '/' . $fotos[$i];

                $query_fotos = "INSERT INTO CA_IMAGENS" . 
                " (caminho, categoria, id_produto) VALUES" .
                " (:caminho, :categoria, :id_produto)";

                $stmt = $this->conn->prepare($query_fotos);

                $stmt->bindValue(":caminho", $path);
                $stmt->bindValue(":categoria", $categoria_foto);
                $stmt->bindValue(":id_produto", $id);

                $stmt->execute();

            }

        } 

    }

    public function remove($veiculo) {
        $query = "DELETE FROM " . $this->table_name . 
        " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // bind parameters
        $stmt->bindParam(':id', $veiculo->getId());

        // execute the query
        if($stmt->execute()){
            return true;
        }    

        return false;
    }

    public function altera($produto, $factory) {

        $msg = "";

        $daoSubcategoria = $factory->getSubcategoriaDao();
        $daoMarca = $factory->getMarcaDao();
        $daoCor = $factory->getCorDao();

        $subcategoria = $daoSubcategoria->buscaPorId(

            intval($_POST['subcategoria'])

        );

        $marca = $daoMarca->buscaPorId(

            intval($_POST['marca'])

        );

        $cor = $daoCor->buscaPorId(

            intval($_POST['cor'])
       
        );

        $query_produto = "UPDATE " . $this->table_name . 
        " SET descricao = :descricao, modelo = :modelo, preco_custo = :preco_custo," .
        " preco_venda = :preco_venda, cd_barras = :cd_barras, cd_referencia = :cd_referencia," .
        " unidade = :unidade, ncm = :ncm, id_marca = :id_marca, id_subcategoria = :id_subcategoria" .
        " WHERE id = :id";

        $stmt = $this->conn->prepare($query_produto);

        $stmt->bindValue(":descricao", $_POST['descricao'] );
        $stmt->bindValue(":modelo", $_POST['descricao'] );
        $stmt->bindValue(":preco_custo", $_POST['preco_custo'] );
        $stmt->bindValue(":preco_venda", $_POST['preco_venda'] );
        $stmt->bindValue(":cd_barras", $_POST['preco_venda'] );
        $stmt->bindValue(':cd_referencia', $_POST['cd_referencia'] );
        $stmt->bindValue(':unidade', $_POST['unidade']  );
        $stmt->bindValue(':ncm', $_POST['ncm'] );
        $stmt->bindValue(':id_marca', $marca->getId() );
        $stmt->bindValue(':id_subcategoria', $subcategoria->getId());
        $stmt->bindValue(':id', $produto->getId() );

        if($stmt->execute()){

            $msg = "Produto alterado com sucesso !";

        } else {

            $msg = "Ocorreu um erro ao tentar alterar o produto !";

        }

        $path_vitrine = "imagens/" . $subcategoria->getNome() . "/" . $produto->getId() . "/Vitrine/";
        $path_detalhe = "imagens/" . $subcategoria->getNome() . "/" . $produto->getId() . "/Detalhes/";


        $msg =  $msg . $this->alteraFoto(

            $produto->getImgsVitrine(), $path_vitrine

        );

        $msg =  $msg . $this->alteraFoto(

            $produto->getImgsDetalhes(), $path_detalhe

        );

        return $msg;

    }

    public function alteraFoto($imagens_prod, $path){

        $erro = "";

        $imagens_cmb = array();
        $imagens_novas = array();

        if(isset($_POST['img_vitrine_cmb']) && count($_FILES['img_produtos']['name']) > 0 && $imagens_prod[0]->getCategoria() == "Vitrine"){

            $imagens_cmb = $_POST['img_vitrine_cmb'];

            $imagens_novas = $_FILES['img_produtos']['name'];
            
        } else if(isset($_POST['img_detalhes_cmb']) && count($_FILES['img_detalhes']['name']) > 0 && $imagens_prod[0]->getCategoria() == "Detalhes"){

            $imagens_cmb = $_POST['img_detalhes_cmb'];

            $imagens_novas = $_FILES['img_detalhes']['name'];

        }

        

        if(count($imagens_cmb) > 0){

            for($i = 0; $i < count($imagens_cmb); $i++){

                $caminho = "";
                $id_img = "";
    
                $query_img = 
                "UPDATE ca_imagens SET caminho = :caminho ".
                "WHERE id = :id";
    
                $stmt = $this->conn->prepare($query_img);

                for($j = 0; $j < count($imagens_prod); $j++){

                    if($imagens_cmb[$i] == $imagens_prod[$j]->getId() ){

                        $id_img = $imagens_prod[$j]->getId();
                        $caminho = $path . $imagens_novas[$i];

                    }

                }
    
                $stmt->bindValue(":id", $id_img );
                $stmt->bindValue(":caminho", $caminho );
    
                if(!$stmt->execute()){
    
                    $erro = ". Ocorreu um erro ao tentar alterar a foto do produto !";
        
                } 
        
            }

        }

        return $erro;

    }

    public function buscaPorId($id) {
        
        $cores = array();
        
        $query = 
        "SELECT pro.id, pro.descricao, pro.modelo, pro.preco_custo, pro.preco_venda,
                pro.cd_barras, pro.cd_referencia, pro.unidade, pro.ncm,
                sub.nome subcategoria, mar.nome marca
            FROM " . $this->table_name . " pro 
            INNER JOIN ca_marcas mar 
                ON mar.id = pro.id_marca 
            INNER JOIN ca_subcategorias sub 
                ON sub.id = pro.id_subcategoria 
        WHERE pro.id = ?";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {

            $produto = new Produto(

                $row['descricao'], $row['modelo'], $row['preco_custo'], $row['preco_venda'],
                $row['cd_barras'], $row['cd_referencia'], $row['unidade'], $row['ncm']

            );

            $produto->setId($row['id']);
            $produto->setMarca($row['marca']);
            $produto->setSubcategoria($row['subcategoria']);

        } 

        /* Select para exibir a relação das cores que o produto possui */

        $query_cor = 
        "SELECT rel_cor.id, ccor.nome cor, ccor.cd_hex hex 
            FROM rel_produto_cor rel_cor 
            INNER JOIN ca_cores ccor 
                ON ccor.id = rel_cor.id_cor 
        WHERE rel_cor.id_produto = ?;";

        $stmt2 = $this->conn->prepare( $query_cor );
        $stmt2->bindValue(1, $id);
        $stmt2->execute();

        if($stmt2){

            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){

                $cores[] = $row2['cor'];

            }

            $produto->setCores($cores);

        }

        $produto->setImgsVitrine($this->buscaImagensProduto($produto->getId(), "Vitrine"));
        $produto->setImgsDetalhes($this->buscaImagensProduto($produto->getId(), "Detalhes"));
        
     
        return $produto;
    }

    /* Select para exibir a relação das imagens que o produto possui */

    public function buscaImagensProduto($id, $categoria_img){

        $imgs = array();

        $query_img = 
        "SELECT img.id, img.caminho, img.categoria, img.id_produto 
            FROM ca_imagens img 
        WHERE img.id_produto = ? AND img.categoria = ?";

        $stmt = $this->conn->prepare( $query_img );
        $stmt->bindValue(1, $id);
        $stmt->bindValue(2, $categoria_img);
        $stmt->execute();

        if($stmt){

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                $path = explode("/", $row['caminho']);

                $img = new Imagem($row['id'], end($path), $categoria_img);
                
                $imgs[] = $img;

                
            }

        }

        return $imgs;

    }

    public function buscaPorNome($nome) {

        $veiculo = null;

        $query = "SELECT
                    id, marca, nome, motor, ano, cor
                FROM
                    " . $this->table_name . "
                WHERE
                    nome = ?
                LIMIT
                    1 OFFSET 0";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $nome);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $veiculo = new Veiculo($row['id'],$row['marca'], $row['nome'], $row['motor'], $row['ano'], $row['cor']);
        } 
     
        return $veiculo;
    }

    public function buscaTodos() {

        $query = 
        "SELECT pro.id, pro.descricao, pro.modelo, pro.preco_custo, pro.preco_venda,
                pro.cd_barras, pro.cd_referencia, pro.unidade, pro.ncm,
                sub.nome subcategoria, mar.nome marca
            FROM " . $this->table_name . " pro 
            INNER JOIN ca_marcas mar 
                ON mar.id = pro.id_marca 
            INNER JOIN ca_subcategorias sub 
                ON sub.id = pro.id_subcategoria 
        ORDER BY pro.id ASC";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
     
        return $stmt;
    }
}
?>