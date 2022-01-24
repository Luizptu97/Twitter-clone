<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;



class AppController extends Action {


    public function timeline(){

       
        $this->validaAutenticacao();
       
        $mensagem = Container::getModel('Mensagem');

        $mensagem->__set('id_usuario', $_SESSION['id']);

        $mensagens = $mensagem->getAll();

        $this->view->mensagens = $mensagens;


        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getUsuarioPorId($_SESSION['id']);
        $this->view->total_mensagens = $usuario->getTotalMensagens();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('timeline');
       
    }


    public function mensagem(){
        
      
        $this->validaAutenticacao();

        print_r($_POST);

        $mensagem = Container::getModel('Mensagem');

        $mensagem->__set('mensagem', $_POST['mensagem']);
        $mensagem->__set('id_usuario', $_SESSION['id']);

        $mensagem->salvar();

        header('Location: /timeline');


    }


    public function validaAutenticacao(){

        session_start();
        if(!isset($_SESSION['id']) || $_SESSION['id'] == '' && !isset($_SESSION['nome']) || $_SESSION['nome'] == ''){
            header('Location: /?login=erro');
            
        }
    }


    public function quemSeguir(){
        $this->validaAutenticacao();

        $usuarioA = Container::getModel('Usuario');
        $usuarioA->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuarioA->getUsuarioPorId($_SESSION['id']);
        $this->view->total_mensagens = $usuarioA->getTotalMensagens();
        $this->view->total_seguindo = $usuarioA->getTotalSeguindo();
        $this->view->total_seguidores = $usuarioA->getTotalSeguidores();



        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = array();

        if($pesquisarPor != ''){
            
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisarPor);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();

        }

        $this->view->usuarios = $usuarios;


        $this->render('quemSeguir');
    }



    public function acao(){
        $this->validaAutenticacao();
       
        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($acao == 'seguir'){
            $usuario->seguirUsuario($id_usuario_seguindo);
 // abaixo: funcao para recuperar os dados e recarregar a pagina com os mesmos parametros na url
            $idget = $usuario->getUsuarioPorId($id_usuario_seguindo);
            header('Location: /quem_seguir?pesquisarPor='. $idget);
           
            
        }else if($acao == 'deixar_de_seguir'){
            $usuario->deixarSeguirUsuario($id_usuario_seguindo);
            $idget = $usuario->getUsuarioPorId($id_usuario_seguindo);
            header('Location: /quem_seguir?pesquisarPor='. $idget);
        
        }


    }



}

?>