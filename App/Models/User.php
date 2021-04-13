<?php

namespace App\Models;

use PDO;

class User extends \Core\Model
{

    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

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

        return $stmt;
    }
	 
	public function validateName()
    {
        if ($this->name == '') {
            $this->errors[] = 'Nazwa jest wymagana';
        }
	}
	
	 public function validateEmail()
    {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Nieprawidłowy adres email';
        }
        if (static::emailExists($this->email)) {
            $this->errors[] = 'Podany adres email jest już zajęty';
        }
	}
	
	 public function validatePassword()
    {
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

    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

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
