<?php

class Model
{
    protected $db;
    protected $tableName;
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->connectDatabase();
    }

    protected function connectDatabase()
    {
        try {
            $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->db->connect_error) {
                throw new Exception("Koneksi database gagal: " . $this->db->connect_error);
            }

            $this->db->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (ENVIRONMENT === 'development') {
                die("Database Error: " . $e->getMessage());
            } else {
                die("Terjadi masalah koneksi database. Silakan coba lagi nanti.");
            }
        }
    }

    public function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    public function query($sql, $params = [])
    {
        try {
            if (!empty($params)) {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $this->db->error);
                }

                $types = '';
                $bind_params = [];
                
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                    $bind_params[] = $param;
                }

                array_unshift($bind_params, $types);
                call_user_func_array([$stmt, 'bind_param'], $this->refValues($bind_params));

                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }

                $result = $stmt->get_result();
                $stmt->close();
                
                return $result;
            } else {
                $result = $this->db->query($sql);
                if (!$result) {
                    throw new Exception("Query failed: " . $this->db->error);
                }
                return $result;
            }
            
        } catch (Exception $e) {
            error_log("Database Query Error: " . $e->getMessage() . " - SQL: " . $sql);
            throw $e;
        }
    }

    private function refValues($arr)
    {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    public function fetchAll($sql, $params = [])
    {
        $result = $this->query($sql, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchOne($sql, $params = [])
    {
        $result = $this->query($sql, $params);
        return $result->fetch_assoc();
    }

    public function insert($data, $table = null)
    {
        $table = $table ?: $this->tableName;
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $this->query($sql, $values);
        
        return $this->db->insert_id;
    }

    public function update($id, $data, $table = null)
    {
        $table = $table ?: $this->tableName;
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $values = array_values($data);
        $values[] = $id;

        $sql = "UPDATE $table SET $setClause WHERE {$this->primaryKey} = ?";
        $this->query($sql, $values);
        
        return $this->db->affected_rows;
    }

    public function delete($id, $table = null)
    {
        $table = $table ?: $this->tableName;
        $sql = "UPDATE $table SET deleted_at = NOW() WHERE {$this->primaryKey} = ?";
        $this->query($sql, [$id]);
        
        return $this->db->affected_rows;
    }

    public function beginTransaction()
    {
        $this->db->begin_transaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollback()
    {
        $this->db->rollback();
    }
}