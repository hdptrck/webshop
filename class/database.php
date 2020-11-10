<?php
class database
{
    private $conn = null;
    private $result = null;
    private $num_of_rows = null;
    private $affected_rows = null;

    // Öffnet die MySQL Connection
    private function open()
    {
        if ($this->conn != null) {
            return false;
        }

        // Verbindung mit dem Server herstellen
        $this->conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

        if ($this->conn->connect_error) {
            // Wenn der Verbindungsaufbau fehlschlägt Fehlermeldung ausgeben.
            die('Connect Error (' . $this->conn->connect_errno . ') ' . $this->conn->connect_error);
        } else {
            return true;
        }
    }

    // Schliesst die MySQL Connection
    private function close_conn()
    {
        if ($this->conn == null) {
            return false;
        }
        if ($this->conn->close()) {
            $this->conn = null;
            return true;
        }
        return false;
    }

    // Datenbank Select Funktion 
    public function select(string $table, array $col = null, array $where = null, array $where_comparison_operators = null, array $where_condition_operators = null, array $where_datatypes = null, int $limit = 1000)
    {
        if (empty($table) || count($where) != count($where_datatypes) || count($where) != count($where_comparison_operators)) {
            return false;
        }

        if (count($where) >= 2) {
            if (count($where) != (count($where_condition_operators) + 1)) {
               return false;
            }
        }

        $this->open();

        $col_formatted = $where_formatted = '';

        if (empty($col)) {
            $col_formatted = '*';
        } else {
            // Array => String, SQL-Felder Namen mit ',' trennen
            $col_formatted = implode(', ', $col);
        }


        // Wenn WHERE Kriterien nicht leer
        if (!empty($where)) {
            // WHERE für Prepared Statement
            $where_formatted = ' WHERE ';
            // Array => String, Datentyp String erstellen
            $where_datatypes_formatted = implode('', $where_datatypes);

            $where_col = $where_values = array();

            // Beide Array mit Daten aus WHERE Parameter befüllen
            foreach ($where as $col => $value) {
                $where_col[] = $col;
                $where_values[] = $value;
            }

            // Datentypen am Anfang hinzufügen
            array_unshift($where_values, $where_datatypes_formatted);

            foreach ($where_col as $key => $value) {
                $where_formatted .= $value . ' ' . $where_comparison_operators[$key] . ' ?';

                if (!empty($where_condition_operators) && array_key_exists($key, $where_condition_operators)) {
                    $where_formatted .= ' ' . $where_condition_operators[$key] . ' ';
                }
            }
        }

        $statement = 'SELECT ' . $col_formatted . ' FROM ' . $table . $where_formatted . ' LIMIT ' . $limit;
        $query = mysqli_prepare($this->conn, $statement);

        if (!empty($where)) {
            // Suchkriterien in das Prepared Statement hinzufügen
            call_user_func_array(
                array($query, "bind_param"),
                $this->ref_values($where_values)
            );
        }

        // Überprüfen ob die Query erfolgreich durchgeführt wurde.
        $return_value = false;

        if ($query->execute()) {
            $this->result = $query->get_result();
            $this->num_of_rows = $this->result->num_rows;
            $return_value = true;
        }

        $this->close_conn();
        return $return_value;
    }

    // Datenbank Insert Funktion
    public function insert(string $table, array $col_names, array $col_values, array $col_values_datatypes)
    {
        // Prüfen ob Angaben leer sind
        if (empty($table) || empty($col_names) || empty($col_values) || empty($col_values_datatypes)) {
            return false;
        }

        // Prüft ob die Länge der Arrays übereinstimmen
        if (count($col_names) != count($col_values) || count($col_names) != count($col_values_datatypes)) {
            echo "ungleich";
            return false;
        }

        $this->open();

        $col_names_formatted = $col_values_placeholders = $col_values_datatypes_formatted = '';

        // Array => String, SQL-Felder Namen mit ',' trennen
        $col_names_formatted = '(' . implode(', ', $col_names) . ')';

        // Array => String, Datentypstring für Prepared Statement
        $col_values_datatypes_formatted = implode('', $col_values_datatypes);

        // Platzhalter für jeden Wert
        foreach ($col_values as $value) {
            $col_values_placeholders .= "?, ";
        }
        // Letzer Leerschlag und Komma entfernen
        $col_values_placeholders = substr($col_values_placeholders, 0, -2);

        //Prepared Statement vorbereiten
        $statement = 'INSERT INTO ' . $table . ' ' . $col_names_formatted . ' VALUES (' . $col_values_placeholders . ')';
        $query = mysqli_prepare($this->conn, $statement);

        // Datentypen hinzufügen
        $formatted = array();
        $formatted[] = $col_values_datatypes_formatted;

        // Werte hinzufügen
        foreach ($col_values as $key => $value) {
            $formatted[] = $value;
        }

        // Mit Prepared Statement verknüpfen
        call_user_func_array(
            array($query, "bind_param"),
            $this->ref_values($formatted)
        );

        // Überprüfen ob die Query erfolgreich durchgeführt wurde.
        $return_value = false;

        if ($query->execute()) {
            $this->affected_rows = $query->affected_rows;
            $return_value = true;
        }

        !$this->close_conn();

        return $return_value;
    }

    private function ref_values($array)
    {
        $refs = array();
        foreach ($array as $key => $value) {
            $refs[$key] = &$array[$key];
        }
        return $refs;
    }

    public function get_result()
    {
        if ($this->result) {
            return $this->result->fetch_assoc();
        }
    }

    public function get_num_of_rows()
    {
        return $this->num_of_rows;
    }
}
