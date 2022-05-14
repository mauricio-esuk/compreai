<?php

 class FornecedoresForm{

    public static function validar(){
        
        $campo = ltrim($_POST['social'], " ");

        $name_spaces = substr_count($campo, " ");

        $fantasia = ltrim($_POST['fantasia'], " ");

        $fantasia_spaces = substr_count($fantasia, " ");


        $email_numbers = preg_match_all( "/[0-9]/", $_POST['email'] );

        $email_dots = substr_count( $_POST['email'], ".");


        if(!preg_match("/^[a-zA-Z  ]+$/", $campo) || $name_spaces > 4 || strlen($campo) < 6){

            return "Erro: A razão social informada está incorreta.\n";

        } else if(!preg_match("/^[a-zA-Z  ]+$/", $fantasia) || $fantasia_spaces > 4 || strlen($fantasia) < 6){

            return "Erro: O nome fantasia informado está incorreta.\n";

        } else if(strlen($_POST['cnpj']) != 14 || !ctype_digit($_POST['cnpj'])){

            return "Erro: O cnpj informado está incorreto.\n";

        } else if(strlen($_POST['ie']) != 11 || !ctype_digit($_POST['ie'])){

            return "Erro: A inscrição estadual (IE) está incorreta.\n";

        } else if(strlen($_POST['telefone']) < 10 || !ctype_digit($_POST['telefone'])){

            return "Erro: O telefone informado está incorreto :::.\n";

        } else if(!preg_match("/^[a-zA-Z0-9.]+$/", $_POST['email']) || $email_numbers > 4 || $email_dots > 1 || 
                strlen($_POST['email']) < 7 || $_POST['provedor'] == "@Provedor"){

            return "Erro: O email informado está incorreto" . $email_numbers . ".\n";

        }  else {

            return "ok";

        }
        
        
        /*else {

            return "ok";

        }

        /*

        if(strlen($_POST['social']) < 6 || ctype_digit($_POST['social'] ) || $name_spaces < 4 ){

            return "Erro: A razão social informada está incorreta.\n";

        }else if(strlen($_POST['fantasia']) < 6 || ctype_digit($_POST['social'])){

            return "Erro: O nome fantasia informado está incorreto.\n";

        } else if(strlen($_POST['cnpj']) != 14 || !ctype_digit($_POST['cnpj'])){

            return "Erro: O cpf informado está incorreto.\n";

        } else if(strlen($_POST['ie']) != 11 || !ctype_digit($_POST['ie'])){

            return "Erro: A inscrição estadual (IE) está incorreta.\n";

        } else if(strlen($_POST['telefone']) < 10 || !ctype_digit($_POST['telefone'])){

            return "Erro: O cartão informado está incorreto :::.\n";

        } else if(strlen($_POST['email']) < 5 || strpos($_POST['email'], "@") !== false || 
            (isset($_POST['provedor']) && $_POST['provedor'] == "@Provedor")){

            return "Erro: O email informado está incorreto.\n";

        } else {

            return "ok";

        }*/

    }

 }

?>