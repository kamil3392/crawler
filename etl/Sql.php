<?php

/**
 * klasa do obsługi MySQL
 */
class Sql
{

    protected static $instance;
    public $connectedDB = null;
    protected $link = null; // link do DB
    protected $result = null; // ostatni result
    protected $last_result = null; // ostatni result tablica
    public $errors = null; // bledy
    public $iErrorNum = 0; // id ostatniego błędu
    public $errorQueries = null; // bledne zapytania

    private function __construct()
    {

    }

// Blokujemy domyślny konstruktor publiczny

    private function __clone()
    {

    }

//Uniemozliwia utworzenie kopii obiektu

    public $query_count = 0;
    public $query_time = 0;
    public $connect_time = 0;
    private $printErrors = false;

    /**
     * PODSTAWOWA METODA
     * sprawdzamy czy jest juz instancja, jesli nie, to tworzymy nowa
     * @return Sql
     */
    public static function getInstance()
    {

        if (self::$instance === null)
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * Wlaczanie wyswietlania bledow
     */
    public function setPrintErrors()
    {
        $this->printErrors = true;
    }

    /**
     * laczenie z baza danych
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $db
     */
    public function connect($host, $user, $password, $db)
    {

        //ponowne laczenie z baza jesli zmiana bazy lub czas ostatniego poleczenia wiekszy od 30 s.
        if ($this->link == null || $db != $this->connectedDB || time() - $this->connect_time >= 30) {

            $this->link = mysqli_connect($host, $user, $password);
            if (!$this->link)
                die('Brak połączenia z bazą!');
            if (!mysqli_select_db($this->link, $db))
                die('Brak dostępu do bazy danych!');
            $this->connectedDB = $db;
			$this->execute("SET NAMES 'utf8'");
            $this->connect_time = time();
        }
    }

    /**
     * destruktor zamyka polaczenie z baza
     */
    public function __destruct()
    {

        if ($this->link) {
            mysqli_close($this->link);
            $this->connectedDB = null;
        }
    }

    /**
     * Zwracanie lacznego czasu wykonywania zapytan
     * @return float
     */
    public function getQueriesTime()
    {
        return round($this->query_time, 7);
    }

    /**
     * Zwracanie liczby wykonanych zapytan
     * @return int
     */
    public function getQueriesNum()
    {
        return $this->query_count;
    }

    /**
     * wykonanie zapytania wraz z pomiarem czasu operacji oraz zapis ewentualnych błędów
     * @param string $query
     */
    public function execute($query, $printErrors = false)
    {

        $time_start = microtime(true);

        $this->result = mysqli_query($this->link, $query);
        $this->query_count++;
        $error = mysqli_error($this->link);
        $this->errors .= $error;
        $this->iErrorNum = mysqli_errno($this->link);

        if (!empty($error)) //dopisywanie blednego zapytania do zmiennej
            $this->errorQueries .= $query . "\n\n";

        if ($this->printErrors === true AND !empty($error)) {
            echo 'ERROR QUERY: ' . $error . '<br>';
            echo 'QUERY: ' . $query . '<br>';
        }

        $time_end = microtime(true);
        $this->query_time += $time_end - $time_start;
    }

    /**
     * metoda może być wywołana po wykonaniu execute().
     * Zwraca tablicę asocjacyjną zawierającą tylko jeden rezultat zapytania
     * @parm string $query
     * @return array
     */
    public function fetch($query = null)
    {

        $data = [];
        if ($query)
            $this->execute($query);

        if ($this->result)
            $data = mysqli_fetch_assoc($this->result);

        return $data;
    }

    /**
     * Metoda zwraca pojedynczą wartosc określoną danym kluczem z pierwszego wiersza zapytania
     * @param String $query
     * @param String $key -> kolumna z ktorej ma byc zwrocona wartosc
     * @return mix
     */
    public function fetchOneValue($query, $key)
    {

        $data = [];
        $this->execute($query);

        if ($this->result)
            $data = mysqli_fetch_assoc($this->result);

        if (isset($data[$key]))
            return $data[$key];

        return null;
    }

    /**
     * metoda może być wywołana po execute() lub bez niego (wtedy zapytanie zawiera się w $query).
     * Zwraca wszystkie rezultaty w obiektowej postaci. W przypadku braku rezultatów zwraca pustą tablicę
     * @param string $query
     * @return array
     */
    public function fetchAll($query = null)
    {

        $data = [];
        if ($query)
            $this->execute($query);

        if (empty($this->errors))
            while ($row = mysqli_fetch_object($this->result)) {
                $data[] = $row;
            }
        return $data;
    }

    /**
     * metoda może być wywołana po execute() lub bez niego (wtedy zapytanie zawiera się w $query).
     * Zwraca wszystkie rezultaty w postaci tablicowej. W przypadku braku rezultatów zwraca pustą tablicę
     * @param string $query
     * @return array
     */
    public function fetchAllAssoc($query = null)
    {

        $data = [];
        if ($query)
            $this->execute($query);

        if (empty($this->errors))
            while ($row = mysqli_fetch_assoc($this->result)) {
                $data[] = $row;
            }
        return $data;
    }

    /**
     * metoda odpowiedzialna za pobieranie danych tylko z jednej kolumny
     * kluczem jest kolejny wiersz wartoscia dane z danej kolumny
     * @param String $column
     * @param String $query
     * @return Array
     */
    public function fetchColumn($column, $query = null)
    {

        $data = [];
        if ($query)
            $this->execute($query);

        if (empty($this->errors))
            while ($row = mysqli_fetch_assoc($this->result)) {
                $data[] = $row[$column];
            }
        return $data;
    }

    /**
     * metoda służy do pobierania DWÓCH kolumn z tabeli. Kolumnę $key zapisuje jako klucz tablicy, a $index
     * jako odpowiadającą mu wartość.
     * @param string $query - zapytanie
     * @param string $value - kolumna wartości
     * @param string $key - kolumna będąca kluczem tablicy wynikowej
     * @return array
     */
    public function fetchIndex($query, $value, $key)
    {

        $data = [];
        $this->execute($query);

        while ($row = mysqli_fetch_assoc($this->result))
            if ($row[$value] != '')
                $data[$row[$key]] = trim($row[$value]);

        return $data;
    }

    /**
     * Metoda przydatna do pobierania danych, dla ktorych jednemu kluczowi
     * moze odpowiadac wiele wartosci.
     * Zwraca tablice z wybranym kluczem i wieloma wartosciami dla niego.
     * @param string $key klucz wynikowej tablicy
     * @param string $value - wartosci tablicy wynikowej
     * @param string $query zapytanie
     * @return array
     */
    public function fetchKeyMultiValue($key, $value, $query = null)
    {
        $data = [];
        if ($query)
            $this->execute($query);

        if (empty($this->errors))
            while ($row = mysqli_fetch_object($this->result))
                $data[$row->$key][] = $row->$value;

        return $data;
    }

    /**
     * Zwracanie tablicy z dwoma przekazanymi kluczami
     * i tablicy wielu wartosci im odpowiadajacym
     * @param string $key
     * @param string $key2
     * @param string $value
     * @param string $query
     * @return array
     */
    public function fetch2KeyMultiValue($key, $key2, $value, $query = null)
    {
        $data = [];
        if ($query)
            $this->execute($query);

        if (empty($this->errors))
            while ($row = mysqli_fetch_object($this->result))
                $data[$row->$key][$row->$key2][] = $row->$value;

        return $data;
    }

    /**
     * Pobiera dane jako pierwszy wymiar tablicy
     * przypisuje wartość dla podanego klucza
     * @param String $key
     * @param String $query
     * @return Array
     */
    public function fetchAllKey($key, $query = null)
    {
        $data = [];
        if ($query)
            $this->execute($query);

        while ($row = mysqli_fetch_assoc($this->result))
            $data[$row[$key]] = $row;

        return $data;
    }

    /**
     * Tworzenie tablicy z danym kluczem, ktoremu moze odpowiadac wiele elementow
     * @param String $key
     * @param String $query
     * @return Array
     */
    public function fetchAllKeyMulti($key, $query = null)
    {
        $data = [];
        if ($query)
            $this->execute($query);

        while ($row = mysqli_fetch_assoc($this->result))
            $data[$row[$key]][] = $row;

        return $data;
    }

    /**
     * Pobiera dane tak ze pierwszy wymiar tablicy to pierwsza wartosc klucza
     * a drugi to druga wartosc klucza
     * @param String $key1
     * @param String $key2
     * @param String $query
     * @return Array
     */
    public function fetchAll2Key($key1, $key2, $query = null)
    {
        $data = [];
        if ($query)
            $this->execute($query);

        while ($row = mysqli_fetch_assoc($this->result))
            $data[$row[$key1]][$row[$key2]] = $row;

        return $data;
    }

    /**
     * Zwraca tablice dwuwymiarowa o podanych kluczach i z podana wartoscia.
     * @param String $key1
     * @param String $key2
     * @param String $value
     * @param String $query
     * @return Array Tablica w postaci $tab[$key1][$key2] = $value
     */
    public function fetchAll2KeyValue($key1, $key2, $value, $query = null)
    {
        $data = [];
        if ($query)
            $this->execute($query);

        while ($row = mysqli_fetch_assoc($this->result))
            $data[$row[$key1]][$row[$key2]] = $row[$value];

        return $data;
    }

    /**
     * metoda zwraca ilość rezultatów otrzymanych od bazy danych w ostatnim zapytaniu
     * @return int
     */
    public function rowsNumber()
    {

        if ($this->result)
            return mysqli_num_rows($this->result);
    }

    /**
     * zwraca insertId ostatniej operacji
     * @return mixed
     */
    public function insertId()
    {

        return mysqli_insert_id($this->link);
    }

    /**
     * zwraca ilość zmienionych wierszy w ostatnim zapytaniu
     * @return int
     */
    public function affectedRows()
    {

        return mysqli_affected_rows($this->link);
    }

    /**
     * rozpoczecie transakcji
     */
    public function startTransaction()
    {
        mysqli_autocommit($this->link, false);
        mysqli_begin_transaction($this->link);
    }

    /**
     * commit transakcji
     */
    public function commitTransaction()
    {
        mysqli_commit($this->link);
        mysqli_autocommit($this->link, true);
    }

    /**
     * rollback transakcji
     */
    public function rollbackTransaction()
    {
        mysqli_rollback($this->link);
        mysqli_autocommit($this->link, true);
    }

    /**
     * laczenie z odpowiednia baza danych podaną w parametrze $dbName. Domyślnie łączenie następuje z bazą
     *
     * @param string $db
     */
    public function selectDB($db)
    {

        if ($db == 'etl') {
            $this->connect('127.0.0.1', 'root', '', $db);
        }
        else {
            die('Podaj nazwe bazy!');
        }
    }

    /**
     * usuwanie znaków niebezpiecznych dla zapytań SQL ze zmiennej $string
     * @param string $string
     * @return string
     */
    public function escape($string)
    {

        return mysqli_escape_string($this->link, $string);
    }

    /**
     * Obsluga procedury zwracajacej dane
     * @param string $query zapytanie wywolujace procedure
     * @return array
     */
    public function fetchArrayProcedure($query)
    {

        $table = [];
        if (mysqli_multi_query($this->link, $query)) {
            do {
                /* store first result set */
                if ($result = mysqli_store_result($this->link)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $table[] = $row;
                    }
                    mysqli_free_result($result);
                }
                if (!mysqli_more_results($this->link)) {
                    break;
                }
            } while (mysqli_next_result($this->link));
        }

        return $table;
    }

    /**
     * Obsluga procedury zwracajacej dane
     * @param string $query zapytanie wywolujace procedure
     * @return array
     */
    public function fetchObjectProcedure($query)
    {

        $table = [];
        if (mysqli_multi_query($this->link, $query)) {
            do {
                /* store first result set */
                if ($result = mysqli_store_result($this->link)) {
                    while ($row = mysqli_fetch_object($result)) {
                        $table[] = $row;
                    }
                    mysqli_free_result($result);
                }
                if (!mysqli_more_results($this->link)) {
                    break;
                }
            } while (mysqli_next_result($this->link));
        }

        return $table;
    }

}

?>
