<?php

include_once('VeiculosDao.php');
include_once('PostgresDao.php');
include_once('ProdutosDao.php');

class PostgresProdutoDao extends PostgresDao implements ProdutosDao {

    private $table_name = 'CA_PRODUTOS';

    public function insere($produto, $factory/* $daoSubcategoria,  $daoMarca */) {

        $msg = "";

        $qtd_fotos = 0;
        $qtd_fotos_detalhes = 0;

        $daoSubcategoria = $factory->getSubcategoriaDao();
        $daoMarca = $factory->getMarcaDao();
        $daoCor = $factory->getCorDao();

        $id = 0;

        if(isset($_POST['enviar']) && (isset($_FILES['img_produtos']) || isset($_FILES['img_detalhes']))){

            $subcategoria = $daoSubcategoria->buscaPorId(
                
                intval($_POST['subcategoria'])

            );

            $marca = $daoMarca->buscaPorId(
                
                intval($_POST['marca'])
            
            );

            $cor = $daoCor->buscaPorId(
                
                intval($_POST['cor'])
            
            );

            $qtd_fotos = count($_FILES['img_produtos']['name']) ;

            $qtd_fotos_detalhes = count($_FILES['img_detalhes']['name']) ;

            if(strlen($_POST['cd_barras']) != 13  || strlen($_POST['ncm']) != 8 || strlen($_POST['cd_referencia']) != 13){

                $msg = $msg . "Erro: O código de barras é composto por 13 números e o NCM por 8 números" . $msg;
    
            } else if(strlen($_POST['modelo']) < 4){

                $msg = $msg . "Erro: O campo Modelo foi preenchido incorretamente" . $msg;

            } else if($_POST['preco_custo'] == 0 || $_POST['preco_venda'] == 0 || strlen($_POST['preco_custo']) > 5){

                $msg = $msg . "Erro: O preço mínimo é R$ 1,00" . $msg;
                
            }

        }

        if(isset($_POST['enviar']) && $msg == ""){

            $produto = new Produto(
                $_POST['descricao'], $_POST['modelo'], 
                intval($_POST['preco_custo']), intval($_POST['preco_venda']), 
                intval($_POST['cd_barras']), intval($_POST['cd_referencia']),
                $_POST['unidade'], intval($_POST['ncm'])
    
            );

            $query_produto = "INSERT INTO " . $this->table_name . 
            " (descricao, modelo, preco_custo, preco_venda, cd_barras, cd_referencia, unidade, ncm, id_marca, id_subcategoria) VALUES" .
            " (:descricao, :modelo, :preco_custo, :preco_venda, :cd_barras, :cd_referencia, :unidade, :ncm, :marca, :subcategoria)";

            $stmt = $this->conn->prepare($query_produto);

            $stmt->bindValue(":descricao", $produto->getDescricao());
            $stmt->bindValue(":modelo", $produto->getModelo());
            $stmt->bindValue(":preco_custo", $produto->getPrecoCusto());
            $stmt->bindValue(":preco_venda", $produto->getPrecoVenda());
            $stmt->bindValue(":cd_barras", $produto->getCdBarras());
            $stmt->bindValue(":cd_referencia", $produto->getCdReferencia());
            $stmt->bindValue(":unidade", $produto->getUnidade()) ;
            $stmt->bindValue(":ncm", $produto->getNcm());
            $stmt->bindValue(":marca", $marca->getId());
            $stmt->bindValue(":subcategoria", $subcategoria->getId());
            
            try{

                if($stmt->execute()){
				
                    $id = $this->conn->lastInsertId();

                    $this->inserirFotos(
                    
                        $qtd_fotos, "Vitrine", $id, 
                        $subcategoria->getNome(),
                        $_FILES['img_produtos']['name']
                    
                    );
                    
                    $this->inserirFotos(
                    
                        $qtd_fotos_detalhes, "Detalhes", $id, 
                        $subcategoria->getNome(),
                        $_FILES['img_detalhes']['name']
                    );

                    $msg = "Produto cadastrado com sucesso!";
				
                }

            }catch(Exception $ex){

                echo $ex->getMessage();

            }

            $query_cor = "INSERT INTO rel_produto_cor (id_produto, id_cor) VALUES (:id_produto, :id_cor)";

            $stmt = $this->conn->prepare($query_cor);

            $stmt->bindValue(":id_produto", $id);
            $stmt->bindValue(":id_cor", $cor->getId());

            try{

                $stmt->execute();

            } catch(Exception $ex){

                echo $ex->getMessage();

            }

        }

        return $msg;

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

        $msg = "Ocorreu um erro ao tentar alterar o produto !";
		$error_cor = false;
		
		$cores_escolhidas = array();
		$cores_escolhidas = $_POST['cor'];
		
		$index_cor_cmb = 0;

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
        $stmt->bindValue(":modelo", $_POST['modelo'] );
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

			$msg = "Produto alterado com sucesso!:::" . (count($cores_escolhidas));

		}
		
		for($i = 0; $i < count($produto->getCores()); $i++) {
			
			$id_rel = $produto->getCores()[$i]->getIdRelProduto() ;
			
			$query_cor_produto = "UPDATE rel_produto_cor SET " .
				"id_produto = :id_produto, id_cor = :id_cor " . 
			" WHERE id =:id";
			
			$stmt_cor = $this->conn->prepare($query_cor_produto);
		
			$stmt_cor->bindValue(":id_produto", $produto->getId());
			$stmt_cor->bindValue(":id_cor", $cores_escolhidas[$index_cor_cmb]);
			$stmt_cor->bindValue(":id", $id_rel);
			
			if(!$stmt_cor->execute() && !$error_cor) {

				$error_cor = true;
				$msg = " Erro relacionado a cor do produto !\n";

			}
			
			$index_cor_cmb++;
			
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
		
		$produto = null;
        
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
        "SELECT rel_cor.id id_rel, ccor.id, ccor.nome cor, ccor.cd_hex hex 
            FROM rel_produto_cor rel_cor 
            INNER JOIN ca_cores ccor 
                ON ccor.id = rel_cor.id_cor 
        WHERE rel_cor.id_produto = ?;";

        $stmt2 = $this->conn->prepare( $query_cor );
        $stmt2->bindValue(1, $id);
        $stmt2->execute();

        if($stmt2){

            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
				
				$cor = new Cor($row2['id'], $row2['cor'], $row2['hex']);
				$cor->setIdRelProduto($row2['id_rel']);

                $cores[] = $cor;

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