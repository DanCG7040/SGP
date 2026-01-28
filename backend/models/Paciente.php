<?php

class Paciente {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $where = '';
        $params = [];
        
        if (!empty($search)) {
            $where = "WHERE p.nombre1 LIKE ? OR p.apellido1 LIKE ? OR p.correo LIKE ? OR p.numero_documento LIKE ?";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }
        
        $sql = "SELECT 
                    p.*,
                    td.nombre as tipo_documento_nombre,
                    g.nombre as genero_nombre,
                    d.nombre as departamento_nombre,
                    m.nombre as municipio_nombre
                FROM paciente p
                INNER JOIN tipos_documento td ON p.tipo_documento_id = td.id
                INNER JOIN genero g ON p.genero_id = g.id
                INNER JOIN departamentos d ON p.departamento_id = d.id
                INNER JOIN municipios m ON p.municipio_id = m.id
                $where
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count($search = '') {
        $where = '';
        $params = [];
        
        if (!empty($search)) {
            $where = "WHERE nombre1 LIKE ? OR apellido1 LIKE ? OR correo LIKE ? OR numero_documento LIKE ?";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }
        
        $sql = "SELECT COUNT(*) as total FROM paciente $where";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT 
                    p.*,
                    td.nombre as tipo_documento_nombre,
                    g.nombre as genero_nombre,
                    d.nombre as departamento_nombre,
                    m.nombre as municipio_nombre
                FROM paciente p
                INNER JOIN tipos_documento td ON p.tipo_documento_id = td.id
                INNER JOIN genero g ON p.genero_id = g.id
                INNER JOIN departamentos d ON p.departamento_id = d.id
                INNER JOIN municipios m ON p.municipio_id = m.id
                WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO paciente (
            tipo_documento_id, numero_documento, nombre1, nombre2,
            apellido1, apellido2, genero_id, departamento_id,
            municipio_id, correo, foto
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['tipo_documento_id'],
            $data['numero_documento'],
            $data['nombre1'],
            $data['nombre2'] ?? null,
            $data['apellido1'],
            $data['apellido2'] ?? null,
            $data['genero_id'],
            $data['departamento_id'],
            $data['municipio_id'],
            $data['correo'],
            $data['foto'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE paciente SET
            tipo_documento_id = ?,
            numero_documento = ?,
            nombre1 = ?,
            nombre2 = ?,
            apellido1 = ?,
            apellido2 = ?,
            genero_id = ?,
            departamento_id = ?,
            municipio_id = ?,
            correo = ?,
            foto = ?
        WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['tipo_documento_id'],
            $data['numero_documento'],
            $data['nombre1'],
            $data['nombre2'] ?? null,
            $data['apellido1'],
            $data['apellido2'] ?? null,
            $data['genero_id'],
            $data['departamento_id'],
            $data['municipio_id'],
            $data['correo'],
            $data['foto'] ?? null,
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM paciente WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function documentoExists($tipoDocId, $numeroDoc, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM paciente 
                WHERE tipo_documento_id = ? AND numero_documento = ?";
        $params = [$tipoDocId, $numeroDoc];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
