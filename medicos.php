


<?php

require_once('conexion.php');
// Habilita CORS para permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");

// Permite los métodos HTTP que deseas permitir (GET, POST, PUT, DELETE, etc.)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// Permite encabezados personalizados (en este caso, Content-Type)
header("Access-Control-Allow-Headers: Content-Type");

// Configura el tipo de contenido de la respuesta
header("Content-Type: application/json");

class MedicosHandler
{



    private $conexion;

    public function __construct()
    {
        global $conexion; // Utiliza la instancia de la conexión definida en conexion.php
        $this->conexion = $conexion;
    }

    public function create($data)
    {

        try {

            $stmt = $this->conexion->prepare("INSERT
         INTO medicos (nombres, apellidos, telefono, carrera, pais) 
         VALUES (:nombres, :apellidos, :telefono, :carrera, :pais)");

            $stmt->bindParam(':nombres', $data['nombres']);
            $stmt->bindParam(':apellidos', $data['apellidos']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':carrera', $data['carrera']);
            $stmt->bindParam(':pais', $data['pais']);

            $stmt->execute();

            echo json_encode("Medico insertado correctamente en la base de datos.");

            $this->conexion = null;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error al insertar el registro: " . $e->getMessage();

        }
    }

    public function read($id)
    {
        // Implementa la lógica para leer un registro de la base de datos basado en el ID proporcionado.
        // Ejemplo: SELECT * FROM tabla WHERE id = ?;

        $sql = $id < 0 ? "SELECT * from medicos WHERE id='$id'" : "SELECT * FROM medicos";
        $query = $this->conexion->prepare($sql);

        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_OBJ);

        $this->conexion = null;

        return $results;
    }

    public function update($id, $data)
    {
        try {

            $stmt = $this->conexion->prepare("UPDATE
          medicos SET nombres= :nombres, apellidos = :apellidos, telefono = :telefono, 
          carrera = :carrera,
           pais = :pais
           WHERE id= :id");

            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':nombres', $data['nombres']);
            $stmt->bindParam(':apellidos', $data['apellidos']);
            $stmt->bindParam(':telefono', $data['telefono']);
            $stmt->bindParam(':carrera', $data['carrera']);
            $stmt->bindParam(':pais', $data['pais']);

            $stmt->execute();

            echo json_encode("Medico editado correctamente en la base de datos.");

            $this->conexion = null;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error al editar el registro: " . $e->getMessage();

        }
    }

    public function delete($id)
    {
        // Implementa la lógica para eliminar un registro de la base de datos basado en el ID proporcionado.
        // Ejemplo: DELETE FROM tabla WHERE id = ?;

        try{
            $sql =  "DELETE from medicos WHERE id='$id'";
            $query = $this->conexion->prepare($sql);
    
            $query->execute();
            echo json_encode('Registro eliminado exitosamente');

        } catch(PDOException $e ){
            http_response_code(500);
            echo "Error al eliminar el registro " . $e->getMessage();

        }
       
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $resourceId = isset($_GET['id']) ? $_GET['id'] : null;

        switch ($method) {
            case 'GET':
                if ($resourceId !== null) {
                    // Manejar solicitud GET para leer un registro específico.
                    $result = $this->read($resourceId);
                    // Devolver el resultado como JSON o HTML, según corresponda.
                } else {
                    // Manejar solicitud GET para obtener una lista de registros.
                    // Ejemplo: SELECT * FROM tabla;
                    $result = $this->read(0);
                    echo json_encode($result);
                }
                break;

            case 'POST':
                // Manejar solicitud POST para crear un nuevo registro.
                // Los datos pueden estar en $_POST o en el cuerpo de la solicitud (dependiendo de cómo se envíen).
                // Obtener el cuerpo de la solicitud.
                $json_data = file_get_contents('php://input');

                // Decodificar el JSON en un array asociativo.
                $data = json_decode($json_data, true);
                $this->create($data);
                break;

            case 'PUT':
                // Manejar solicitud PUT para actualizar un registro existente.
                // Los datos pueden estar en el cuerpo de la solicitud (dependiendo de cómo se envíen).
                $data = json_decode(file_get_contents('php://input'), true);
                $this->update($resourceId, $data);
                break;

            case 'DELETE':
                // Manejar solicitud DELETE para eliminar un registro existente.
                $this->delete($resourceId);
                break;

            default:
                // Manejar otros métodos HTTP según sea necesario.
                break;
        }
    }
}

// Uso de la clase CRUDHandler para manejar solicitudes CRUD.
$medicosHandler = new MedicosHandler();
$medicosHandler->handleRequest();
?>
