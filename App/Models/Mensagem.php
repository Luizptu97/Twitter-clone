<?php

namespace App\Models;
use MF\Model\Model;

class Mensagem extends Model {

    private $id;
    private $id_usuario;
    private $mensagem;
    private $data;

    public function __get($atributo){
        return $this->$atributo;
    }

    public function __set($atributo, $valor){
        $this->$atributo = $valor;
    }



    public function salvar(){

        $query = "insert into mensagens(id_usuario, mensagem)values(:id_usuario, :mensagem)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':mensagem', $this->__get('mensagem'));
        $stmt->execute();

        return $this;
    }


    public function getAll(){

        $query = "
            select 
                m.id, m.id_usuario, u.nome, m.mensagem, DATE_FORMAT(m.data, '%d/%m/%Y %H:%i') as data
            from 
                mensagens as m
                left join usuarios as u on (m.id_usuario = u.id)
            where 
                m.id_usuario = :id_usuario
                or m.id_usuario in (select id_usuario_seguindo from usuarios_seguidores
                where id_usuario = :id_usuario)            
            order by
                m.data desc
            "; // recupera todas as mensagens
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }












}


?>