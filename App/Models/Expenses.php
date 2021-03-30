<?php

namespace App\Models;

use PDO;

class Expenses extends \Core\Model
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

			$sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
                    VALUES (:userID, :expenseCategory, :paymentMethod, :expenseAmount, :expenseDate, :expenseComment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
            $stmt->bindValue(':expenseCategory', $this->expenseCategory, PDO::PARAM_STR);
			$stmt->bindValue(':paymentMethod', $this->paymentMethod, PDO::PARAM_STR);
			$stmt->bindValue(':expenseAmount', $this->amountExpense, PDO::PARAM_STR);
            $stmt->bindValue(':expenseDate', $this->dateExpense, PDO::PARAM_STR);
            $stmt->bindValue(':expenseComment', $this->expenseComment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	 public static function getExpenseCategoriesAssigned()
    {
		$sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id=:userID';
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
		
		$stmt->execute();
		$incomeCategories = $stmt->fetchAll();
		
		return $incomeCategories;
	}
	
	 public static function getPaymentMethodsAssigned()
    {
		$sql = 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id=:userID';
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
		
		$stmt->execute();
		$paymentMethods = $stmt->fetchAll();
			
		return $paymentMethods;
	}
	
	public static function copyDefaultCategories($userID)
    {
		$sql = 'INSERT INTO expenses_category_assigned_to_users (id, user_id, name) SELECT "NULL", :userID, name FROM expenses_category_default';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', $userID, PDO::PARAM_STR);
		$stmt->execute();
	}
	
	public static function copyDefaultPaymentMethods($userID)
    {
		$sql = 'INSERT INTO payment_methods_assigned_to_users (id, user_id, name) SELECT "NULL", :userID, name FROM payment_methods_default';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', $userID, PDO::PARAM_STR);
		$stmt->execute();
	}
	
    public function validate()
    {
        // Amount
       
		if ($this->amountExpense == '') {
            $this->errors[] = 'Wpisz kwotę wydatku';
        }
	   elseif ($this->amountExpense <= 0) {
            $this->errors[] = 'Kwota wydatku nie może być mniejsza lub równa zero';
        }

        // Date of income
		
		if ($this->dateExpense == '') {
            $this->errors[] = 'Wprowadź datę';
        }
		
		if ($this->dateExpense > date("Y-m-d")) {
            $this->errors[] = 'Data przychodu nie może wykraczać poza dzisiejszą datę.';
        }
		
        }
		
		
		
	public static function getCategoryData($categoryID)
    {
		
		
		
		
		$sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE id=:id';
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $categoryID, PDO::PARAM_STR);
		
		$stmt->execute();
		$category = $stmt->fetchAll();

		return $category;
		
	}
	
	public static function getValue()
    {
		
		
		$v = intval($_GET['v']);

		return $v;
	}
	
	public static function getCategory()
    {
		
		
		$c = intval($_GET['c']);

		return $c;
	}
	
	public static function getThisMonthExpensesFromDB($category)
    {
		$sql = "SELECT SUM(amount) FROM expenses WHERE user_id=:userID AND expense_category_assigned_to_user_id =:category AND Year(date_of_expense) = Year(Now()) And Month(date_of_expense) = Month(Now())";
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
		$stmt->bindValue(':category', $category, PDO::PARAM_STR);
		
		
		$stmt->execute();
		$expenses = $stmt->fetchAll();
		
		return $expenses;
	}
	
		public static function checkIfIsInCurrentMonth($date)
    {
		
		if((date("m", strtotime($date)) == date("m")) && (date("Y", strtotime($date)) == date("Y")))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
       
    }
