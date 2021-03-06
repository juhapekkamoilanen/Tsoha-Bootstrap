<?php

class Person extends BaseModel{
	//Attribuutit
	public $user_id, $username, $password, $email, $full_name, $user_info;
	//Konstruktori
	public function __construct($attributes) {
		parent::__construct($attributes);
		//validators sisältää validaattoreiden NIMET merkkijonoina
		$this->validators = array(
			'validate_username', 'validate_password', 'validate_email');
	}

	// Lisää uusi käyttäjä järjestelmään
	
	public function save(){
		// Lisätään RETURNING id tietokantakyselymme loppuun, niin saamme lisätyn rivin id-sarakkeen arvon
	    $query = DB::connection()
	    	->prepare('	INSERT INTO Person 	(username, password, email, full_name, user_info) 
	    				VALUES 				(:u, :p, :e, :f, :i) 
	    									RETURNING user_id'
	    );
	    $query->execute(array(	'u' => $this->username, 
	    						'p' => $this->password, 
	    						'e' => $this->email,
	    						'f' => $this->full_name,
	    						'i' => $this->user_info
	    ));
	    // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
	    // id-sarakkeeseen generoituu SERIAL luku id:ksi queryn suorituksen yhteydessä
	    $row = $query->fetch();
	    // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
	    $this->user_id = $row['user_id'];

	    return $this->user_id;
	}
	
	public static function all(){
		//Alustetaan kysely tietokantayhteydellämme
		$query = DB::connection()->prepare('SELECT * FROM Person');
		//Suoritetaan kysely
		$query->execute();
		//tallennetaan rivit kyselystä
		$rivit = $query->fetchAll();

		//alustetaan muuttuja vaatteille
		$people = Array();

		//käydään rivit läpi ja lisätään items-taulukkoon
		foreach ($rivit as $rivi) {
			//tallennetaan kyselyn rivi taulukkoon item-oliona
			$people[] = new person(array(
				'user_id' => $rivi['user_id'],
				'username' => $rivi['username'],
				'password' => $rivi['password'],
				'email' => $rivi['email'],
				'full_name' => $rivi['full_name'],
				'user_info' => $rivi['user_info'],
			));
		}


		return $people;

	}

	public static function find($id){
		//Haetaan tietokannasta Person-taulusta ne rivit joissa user_id on parametrinä annettu
	    $query = DB::connection()->prepare('SELECT * FROM Person WHERE user_id = :user_id LIMIT 1');
	    //Suoritetaan kysely
	    $query->execute(array('user_id' => $id));
	    //Tallennetaan kyselyn ensimmäinen (ainoa) rivi
	    $row = $query->fetch();

	    //jos siinä on sisältöä niin luodaan olio ja palautetaan se
	    if($row){
	      	$person = new Person(array(
					'user_id' => $row['user_id'],
					'username' => $row['username'],
					'password' => $row['password'],
					'email' => $row['email'],
					'full_name' => $row['full_name'],
					'user_info' => $row['user_info'],
					));

	      	return $person;
	    }

	    return null;
  	}

  	public function update() {
		$query = DB::connection()
	    	->prepare('	UPDATE 	Person
	    				SET 	username = :username,
	    						password = :password,
	    						email = :email,
	    						full_name = :full_name,
	    						user_info = :user_info
						WHERE 	user_id = :user_id'
	    );

	    $query->execute(array(	
	    						'username' => $this->username,
	    						'password' => $this->password,
	    						'email' => $this->email,
	    						'full_name' => $this->full_name,
	    						'user_info' => $this->user_info,
	    						'user_id' => $this->user_id
	    ));
	    
  	}

  	public static function authenticate($username, $password) {
  		$query = DB::connection()->prepare('SELECT * FROM Person
  											WHERE username = :username
  											AND password = :password
  											LIMIT 1');
  		//Suoritetaan kysely
	    $query->execute(array('username' => $username, 'password' => $password));
	    //Tallennetaan kyselyn ensimmäinen (ainoa) rivi
	    $row = $query->fetch();
  		//jos siinä on sisältöä niin luodaan olio ja palautetaan se
	    return self::create_object($row);

  	}

  	private static function create_object($db_row) {
  		if($db_row){
	      	$person = new Person(array(
					'user_id' => $db_row['user_id'],
					'username' => $db_row['username'],
					'password' => $db_row['password'],
					'email' => $db_row['email'],
					'full_name' => $db_row['full_name'],
					'user_info' => $db_row['user_info'],
					));

	      	return $person;
	    }

	    return null;
  	}

  	public static function add_item_for_person($item_id, $person_id){
        $add_query = DB::connection()
    			->prepare('INSERT INTO Wardrobe (fk_wardrobe_person, fk_wardrobe_item)
    				VALUES (:person_id, :item_id');
        $add_query->execute(array('person_id' => $person_id, 'item_id' => $item_id));
            
    }

    public function destroy() {
  		$query = DB::connection()
  			->prepare(' DELETE from Person
  						WHERE user_id = :user_id');
		$query->execute(array('user_id' => $this->user_id));
  	}

    //Validators

  	public function validate_username() {
  		//tarkastetaan että ei ole tyhja ja pidempi kuin 3
  		$desc = 'Username';
  		$input = $this->username;
  		$length = 3;
  		$errors = parent::validate_string_length($desc, $input, $length);
  		return $errors;
  	}

  	public function validate_password() {
  		//tarkastetaan että ei ole tyhja ja pidempi kuin 3
  		$desc = 'Password';
  		$input = $this->password;
  		$length = 3;
  		$errors = parent::validate_string_length($desc, $input, $length);
  		return $errors;
  	}

  	public function validate_email() {
  		//tarkastetaan että ei ole tyhja ja pidempi kuin 3
  		$desc = 'Email';
  		$input = $this->email;
  		$length = 3;
  		$errors = parent::validate_string_length($desc, $input, $length);
  		return $errors;
  	}



}