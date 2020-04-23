<?php
    namespace utils;

    class Utility implements \JsonSerializable {
		
        public function jsonSerialize() {            
            $class = get_class($this);      // Ottiene il nome della classe
            $json = array();
            $properties = (array) $this;    // Trasforma l'oggetto in un Array
            // var_dump($properties);

            $keys = array_keys($properties);    // Ottiene il nome di ogni key dell'array
                                                // N.B: ogni key, secondo il casting da Obj ad Arr, mette davanti a ogni key
                                                // il nome della classe (es. "Comuneid")
            // var_dump($keys);
            $plength = count($keys);        // Ottiene il numero delle key dell'array
            
            for ($i = 0; $i < $plength; $i++) {
                $property = substr($keys[$i], strlen($class) + 2);     // Ricava il nome della key con la prima lettera UpperCase (per la notazione "cammellare")
                                                                                // N.B: in posizione 0 viene dato un carattere vuoto, quindi +2 (e non +1)

                $method = "get" . ucfirst($property);        // Stringa che rappresenta il metodo
                // var_dump($method);
                if (method_exists($this, $method)) {        // Se esiste il metodo per l'oggetto lo inserisce nell'array 
                    $json[$property] = $this->$method();    // con la chiave ottenuta prima
                }
            }
        
            return $json;
        }


        public static function jsonToObject($json, $className) {        // json è un oggetto in notazione JSON
            $className = ucfirst($className);       // Per sicurezza, lo metto
            $obj = new $className();        // Creo l'oggetto con il nome della classe data

            foreach ($json as $key => $value) {
                $setter = "set" . ucfirst($key);        // Creare il metodo Setter

                if( method_exists($obj, $setter) ) {
                    $obj->$setter($value);      // Popolo l'oggetto con i valori
                }
            }

            return $obj;        // Well Done  :)

        }
        
        
        public static function createWhere(Array $params, String $table, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, Array $joinTablesWithOnColumns = null, String $tableJoinColumn = null, String $select = '*') {
			$counter = 0;
            $query = "SELECT $select FROM $table";

            if(!is_null($joinTablesWithOnColumns)){
                foreach($joinTablesWithOnColumns as $tableToJoin => $column){
                    if(!is_null($column)){
                        $query .= " JOIN $tableToJoin ON($table.$tableJoinColumn = $tableToJoin.$column)";
                    }
                }
            }
                        
			if( !is_null($params) )  {
				foreach($params as $key => $value) {
					if( !is_null($value) ) {        // Da vedere meglio, empty non andava bene
						if($counter == 0) {
							$query .= " WHERE $key = :$key";
						} else {
                            if($orClause) {
                                $query .= " OR $key = :$key";
                            } else {
                                $query .= " AND $key = :$key";
                            }
						}

						$counter ++;
					}
				}
            }

            if(isset($orderBy)){
                $query .= " ORDER BY $orderBy";
            }
            
            if($replaceWithLIKE) {
                $query = str_replace("=", "LIKE", $query);
            }

			return $query;
        }
        
    }
?>
