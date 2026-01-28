<?php

class PacienteController {
    private $pacienteModel;

    public function __construct() {
        $this->pacienteModel = new Paciente();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $pacientes = $this->pacienteModel->getAll($page, $limit, $search);
        $total = $this->pacienteModel->count($search);

        Response::success([
            'pacientes' => $pacientes,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    }

    public function show($id) {
        $paciente = $this->pacienteModel->findById($id);
        
        if (!$paciente) {
            Response::notFound('Paciente no encontrado');
        }

        Response::success($paciente);
    }

    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = $this->validatePaciente($data);
        if (!empty($errors)) {
            Response::error('Error de validación', 400, $errors);
        }

        if ($this->pacienteModel->documentoExists($data['tipo_documento_id'], $data['numero_documento'])) {
            Response::error('El número de documento ya existe', 400);
        }

        $id = $this->pacienteModel->create($data);
        $paciente = $this->pacienteModel->findById($id);

        Response::success($paciente, 'Paciente creado correctamente', 201);
    }

    public function update($id) {
        $paciente = $this->pacienteModel->findById($id);
        
        if (!$paciente) {
            Response::notFound('Paciente no encontrado');
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $errors = $this->validatePaciente($data, $id);
        if (!empty($errors)) {
            Response::error('Error de validación', 400, $errors);
        }

        if ($this->pacienteModel->documentoExists($data['tipo_documento_id'], $data['numero_documento'], $id)) {
            Response::error('El número de documento ya existe', 400);
        }

        $this->pacienteModel->update($id, $data);
        $paciente = $this->pacienteModel->findById($id);

        Response::success($paciente, 'Paciente actualizado correctamente');
    }

    public function delete($id) {
        $paciente = $this->pacienteModel->findById($id);
        
        if (!$paciente) {
            Response::notFound('Paciente no encontrado');
        }

        if ($this->pacienteModel->delete($id)) {
            Response::success(null, 'Paciente eliminado correctamente');
        } else {
            Response::error('Error al eliminar paciente', 500);
        }
    }

    private function validatePaciente($data, $excludeId = null) {
        $errors = [];

        $required = Validator::required($data, [
            'tipo_documento_id',
            'numero_documento',
            'nombre1',
            'apellido1',
            'genero_id',
            'departamento_id',
            'municipio_id',
            'correo'
        ]);

        if ($required !== true) {
            $errors = array_merge($errors, $required);
        }

        if (isset($data['correo']) && !Validator::email($data['correo'])) {
            $errors['correo'] = 'El correo electrónico no es válido';
        }

        if (isset($data['numero_documento']) && !Validator::documento($data['numero_documento'])) {
            $errors['numero_documento'] = 'El número de documento no es válido';
        }

        if (isset($data['nombre1']) && !Validator::length($data['nombre1'], 2, 50)) {
            $errors['nombre1'] = 'El primer nombre debe tener entre 2 y 50 caracteres';
        }

        if (isset($data['apellido1']) && !Validator::length($data['apellido1'], 2, 50)) {
            $errors['apellido1'] = 'El primer apellido debe tener entre 2 y 50 caracteres';
        }

        return $errors;
    }
}
