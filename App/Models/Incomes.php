<?php

namespace App\Models;

use PDO;

class Incomes extends \Core\Model
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

			$sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                    VALUES (:userID, :incomeCategory, :incomeAmount, :incomeDate, :incomeComment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
            $stmt->bindValue(':incomeCategory', $this->incomeCategory, PDO::PARAM_STR);
			$stmt->bindValue(':incomeAmount', $this->incomeAmount, PDO::PARAM_STR);
            $stmt->bindValue(':incomeDate', $this->incomeDate, PDO::PARAM_STR);
            $stmt->bindValue(':incomeComment', $this->incomeComment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	 public static function getIncomeCategoriesAssigned()
    {
		$sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id=:userID';
		
		$db = static::getDB();
        $getIncomeCategories = $db->prepare($sql);
		$getIncomeCategories->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
		
		$getIncomeCategories->execute();
		$incomeCategories = $getIncomeCategories->fetchAll();
		
		
		return $incomeCategories;
	}
	
	public static function copyDefaultCategories($userID)
    {
		$sql = 'INSERT INTO incomes_category_assigned_to_users (id, user_id, name) SELECT "NULL", :userID, name FROM incomes_category_default';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', $userID, PDO::PARAM_STR);
		$stmt->execute();
	}

    public function validate()
    {
		if ($this->incomeAmount == '') {
            $this->errors[] = 'Wpisz kwotę przychodu';
        }
	   elseif ($this->incomeAmount <= 0) {
            $this->errors[] = 'Kwota przychodu nie może być mniejsza lub równa zero';
        }
		
		if ($this->incomeDate == '') {
            $this->errors[] = 'Wprowadź datę';
        }
		
		if ($this->incomeDate > date("Y-m-d")) {
            $this->errors[] = 'Data przychodu nie może wykraczać poza dzisiejszą datę.';
        }

    }    
}
