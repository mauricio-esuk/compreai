<?php

include_once('UsuariosDao.php');
include_once('PostgresDao.php');

include_once('validacoes/UsuariosForm.php');

class PostgresUsuarioDao extends PostgresDao implements UsuariosDao {

    private $table_name = 'usuarios';
    
    public function insere($usuario) {
       
        if(isset($_POST['env'])){

            // $us_cpf, $us_email, $us_nome, $us_rg, $us_celular, $us_telefone,$us_senha, $us_cartao

            $usuario = new Usuario(

                $_POST['cpf'], $_POST['email'],
                trim($_POST['nome'], " "), $_POST['rg'],
                $_POST['celular'], $_POST['telefone'],
                $_POST['senha'], $_POST['cartao']

            );

            $usuario->setProvedor($_POST['provedor']);

            $query = "INSERT INTO " . $this->table_name . 
            " (nome, email, senha, cpf, celular1, celular2, rg, cartao) VALUES" .
            " (:nome, :email, :senha, :cpf, :celular1, :celular2, :rg, :cartao)";

            $stmt = $this->conn->prepare($query);

            // bind values 
            $stmt->bindValue(":nome", $usuario->getNome() );
            $stmt->bindValue(":email", $usuario->getEmail() . $usuario->getProvedor() );
            $stmt->bindValue(":senha", $usuario->getSenha() );
            $stmt->bindValue(":cpf", $usuario->getCPF() );
            $stmt->bindValue(":celular1", $usuario->getCelular() );
            $stmt->bindValue(":celular2", $usuario->getTelefone() );
            $stmt->bindValue(":rg", $usuario->getRG() );
            $stmt->bindValue(":cartao", $usuario->getCartao() );

            if(UsuariosForm::validar($usuario) == "ok" && $stmt->execute()){

                return "Usuário cadastrado com sucesso";

            }else{

                return UsuariosForm::validar($usuario);

            }

        } else if(isset($_POST['env'])){

            return UsuariosForm::validar($usuario);

        }    
    }

    public function removePorId($id) {
        $query = "DELETE FROM " . $this->table_name . 
        " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // bind parameters
        $stmt->bindParam(':id', $id);

        // execute the query
        if($stmt->execute()){
            return true;
        }    

        return false;
    }

    public function remove($usuario) {
        return removePorId($usuario->getId());
    }

    public function altera($usuario) {

        $query = "UPDATE " . $this->table_name . 
        " SET cpf = :cpf, senha = :senha, nome = :nome" .
        " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // bind parameters
        $stmt->bindValue(":cpf", $usuario->getCpf());
        $stmt->bindValue(":senha", md5($usuario->getSenha()));
        $stmt->bindValue(":nome", $usuario->getNome());
        $stmt->bindValue(':id', $usuario->getId());

        // execute the query
        if($stmt->execute()){
            return true;
        }    

        return false;
    }

    public function buscaPorId($id) {
        
        $usuario = null;

        $query = "SELECT
                    id, cpf, nome, senha
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    1 OFFSET 0";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $usuario = new Usuario($row['id'],$row['cpf'], $row['senha'], $row['nome']);
        } 
     
        return $usuario;
    }

    public function buscaPorLogin($login) {

        $usuario = null;

        $query = "SELECT
                    id, login, nome, senha
                FROM
                    " . $this->table_name . "
                WHERE
                    login = ?
                LIMIT
                    1 OFFSET 0";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->bindValue(1, $login);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
		
        if($row) {
			
            $usuario = new Usuario($row['id'],$row['login'], $row['senha'], $row['nome']);
			
        } 
     
        return $usuario;
    }

    /*
    public function buscaTodos() {
        $query = "SELECT
                    id, login, senha, nome
                FROM
                    " . $this->table_name . 
                    " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
     
        return $stmt;
    }
    */

    public function buscaTodos() {

        $usuarios = array();

        $query = "SELECT
                    id, cpf, senha, nome
                FROM
                    " . $this->table_name . 
                    " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			
            extract($row);
			
            $usuarios[] = new Usuario($id,$cpf,$senha,$nome);
			
        }
        
        return $usuarios;
    }
	
	public function buscaPorCPF() {

        $usuario = null;
        $login_encontrado = false;

        $query = "SELECT cpf, email, nome, rg, celular1, celular2, senha, cartao FROM " . $this->table_name . "
            WHERE
                cpf = ?
            AND
                senha = ?
        LIMIT 1 OFFSET 0";
    
        $stmt = $this->conn->prepare( $query );

        $stmt->bindValue(1, $_POST['cpf'] );
        $stmt->bindValue(2, $_POST["senha"]);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {

            $login_encontrado = true;

        } 

        if($login_encontrado){


            // este return "<script type = 'text/javascript' >alert('Usuário encontrado !')</script>";

            //return new Usuario($row['cpf'],$row['email'], $row['nome'], $row['rg'], $row['celular1'], $row['celular2'], $row['senha'], $row['cartao']);

        } else {


            // este return "<script type = 'text/javascript' >alert('Usuário não encontrado !')</script>";

        }
        
        return $login_encontrado;
    }
}
?>