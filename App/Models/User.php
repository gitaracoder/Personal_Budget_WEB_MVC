<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (name, email, password_hash)
                    VALUES (:name, :email, :password_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			

            return $stmt->execute();
        }

        return false;
    }
	
	public function updateName()
    {
        $this->validateName();

        if (empty($this->errors)) {

            $sql = 'UPDATE users
					SET name = :name
					WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	public function updateEmail()
    {
        $this->validateEmail();

        if (empty($this->errors)) {

            $sql = 'UPDATE users
					SET email = :email
					WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	public function updatePassword()
    {
		$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        $this->validatePassword();

        if (empty($this->errors)) {

            $sql = 'UPDATE users
					SET password_hash = :password_hash
					WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	public function chceckNewUserID()
    {
       
			
		$sql = 'SELECT * FROM users WHERE email = :email';
		
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
		
		$stmt->execute();
		$stmt = $stmt->fetchColumn();

		//echo $stmt; 

        return $stmt;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
	 
	 public function validateName()
    {
		// Name
        if ($this->name == '') {
            $this->errors[] = 'Nazwa jest wymagana';
        }
	}
	
	 public function validateEmail()
    {
		// email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Nieprawidłowy adres email';
        }
        if (static::emailExists($this->email)) {
            $this->errors[] = 'Podany adres email jest już zajęty';
        }
	}
	
	 public function validatePassword()
    {
		// Password
        if (strlen($this->password) < 6) {
            $this->errors[] = 'Hasło musi składać się z co najmniej 6 znaków';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło musi zawierać co najmniej jedną literę';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło musi zawierać co najmniej jedną cyfrę';
        }
	}
	 
	 
    public function validate()
    {    
		$this->validateName();
		$this->validateEmail();
		$this->validatePassword();
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
}
