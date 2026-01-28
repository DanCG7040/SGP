<?php

class Catalogo {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getDepartamentos() {
        $stmt = $this->db->query("SELECT * FROM departamentos ORDER BY nombre");
        return $stmt->fetchAll();
    }

    public function getMunicipiosByDepartamento($departamentoId) {
        $stmt = $this->db->prepare("SELECT * FROM municipios WHERE departamento_id = ? ORDER BY nombre");
        $stmt->execute([$departamentoId]);
        return $stmt->fetchAll();
    }

    public function getTiposDocumento() {
        $stmt = $this->db->query("SELECT * FROM tipos_documento ORDER BY nombre");
        return $stmt->fetchAll();
    }

    public function getGeneros() {
        $stmt = $this->db->query("SELECT * FROM genero ORDER BY nombre");
        return $stmt->fetchAll();
    }
}
