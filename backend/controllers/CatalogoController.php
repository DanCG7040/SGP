<?php

class CatalogoController {
    private $catalogoModel;

    public function __construct() {
        $this->catalogoModel = new Catalogo();
    }

    public function getDepartamentos() {
        $departamentos = $this->catalogoModel->getDepartamentos();
        Response::success($departamentos);
    }

    public function getMunicipios() {
        $departamentoId = isset($_GET['departamento_id']) ? (int)$_GET['departamento_id'] : 0;
        
        if ($departamentoId <= 0) {
            Response::error('El ID del departamento es requerido', 400);
        }

        $municipios = $this->catalogoModel->getMunicipiosByDepartamento($departamentoId);
        Response::success($municipios);
    }

    public function getTiposDocumento() {
        $tipos = $this->catalogoModel->getTiposDocumento();
        Response::success($tipos);
    }

    public function getGeneros() {
        $generos = $this->catalogoModel->getGeneros();
        Response::success($generos);
    }
}
