<?php

namespace App\Models;
use \App\Models\Expenses;
use \App\Models\Incomes;

use PDO;

class Setting extends \Core\Model
{
	
	public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function checkIfPaymentMethodAlreadyExists()
    {
		$existingPaymentMethods = Expenses::getPaymentMethodsAssigned();
		$result = false;
		
		foreach ($existingPaymentMethods as $item) 
		{
			if (mb_strtoupper($item['name'],"UTF-8") == mb_strtoupper($this->newPaymentMethod,"UTF-8"))
			{
				$result = $result || true;
			}
			else
			{
				$result = $result || false;
			}
		}
		
		return $result;
	}
	
	public function addPaymentMethod()
    {
		$sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
                    VALUES (:userID, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':name', $this->newPaymentMethod, PDO::PARAM_STR);
            

            return $stmt->execute();
	}
	
	public function updatePaymentMethod()
    {
		$sql = 'UPDATE payment_methods_assigned_to_users
				SET name = :newName
				WHERE user_id = :userID AND name = :oldName';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':newName', $this->newPaymentMethod, PDO::PARAM_STR);
			 $stmt->bindValue(':oldName', $this->oldPaymentMethod, PDO::PARAM_STR);
            

            return $stmt->execute();
	}
	
	public function deletePaymentMethod()
    {
		$sql = 'SELECT id FROM payment_methods_assigned_to_users
                    WHERE user_id = :userID AND name = :name';
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
		$stmt->bindValue(':name', $this->methodToDelete, PDO::PARAM_STR);
			
		$stmt->execute();
		$categories = $stmt->fetchAll();
		$categoryNumber = $categories[0]['id'];

		
		$sql = 'DELETE FROM payment_methods_assigned_to_users
                    WHERE user_id = :userID AND name = :name';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':name', $this->methodToDelete, PDO::PARAM_STR);
            

            $stmt->execute();
			
			$sql = 'DELETE FROM expenses
                    WHERE user_id = :userID AND payment_method_assigned_to_user_id = :payment_method_assigned_to_user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':payment_method_assigned_to_user_id', $categoryNumber, PDO::PARAM_STR);


            $stmt->execute();
			
			
	}
	
	 public function checkIfIncomeCategoryAlreadyExists()
    {
		$existingIncomeCategories = Incomes::getIncomeCategoriesAssigned();
		$result = false;
		
		foreach ($existingIncomeCategories as $item) 
		{
			if (mb_strtoupper($item['name'],"UTF-8") == mb_strtoupper($this->newIncomeCategory,"UTF-8"))
			{
				$result = $result || true;
			}
			else
			{
				$result = $result || false;
			}
		}
		
		return $result;
	}
	
	public function addIncomeCategory()
    {
		$sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
                    VALUES (:userID, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':name', $this->newIncomeCategory, PDO::PARAM_STR);
            

            return $stmt->execute();
	}
	
	public function updateIncomeCategory()
    {
		$sql = 'UPDATE incomes_category_assigned_to_users
				SET name = :newName
				WHERE user_id = :userID AND name = :oldName';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':newName', $this->newIncomeCategory, PDO::PARAM_STR);
			 $stmt->bindValue(':oldName', $this->oldIncomeCategory, PDO::PARAM_STR);
            

            return $stmt->execute();
	}
	
	public function deleteIncomeCategory()
    {
		$sql = 'SELECT id FROM incomes_category_assigned_to_users
                    WHERE user_id = :userID AND name = :name';
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
		$stmt->bindValue(':name', $this->methodToDelete, PDO::PARAM_STR);
			
		
		$stmt->execute();
		$categories = $stmt->fetchAll();
		//var_dump($categories);
		$categoryNumber = $categories[0]['id'];
		
		//echo $categories[0]['id'];
		
		
		
		$sql = 'DELETE FROM incomes_category_assigned_to_users
                    WHERE user_id = :userID AND name = :name';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':name', $this->methodToDelete, PDO::PARAM_STR);
            

            $stmt->execute();
			
			$sql = 'DELETE FROM incomes
                    WHERE user_id = :userID AND income_category_assigned_to_user_id = :income_category_assigned_to_user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':income_category_assigned_to_user_id', $categoryNumber, PDO::PARAM_STR);
			 
		
            

            $stmt->execute();
			
			
	}
	
	
	public function checkIfExpenseCategoryAlreadyExists()
    {
		$existingExpenseCategories = Expenses::getExpenseCategoriesAssigned();
		$result = false;
		
		foreach ($existingExpenseCategories as $item) 
		{
			if (mb_strtoupper($item['name'],"UTF-8") == mb_strtoupper($this->newExpenseCategory,"UTF-8"))
			{
				$result = $result || true;
			}
			else
			{
				$result = $result || false;
			}
		}
		
		if(isset($this->oldExpenseCategory))
		{
			if($this->newExpenseCategory == $this->oldExpenseCategory)
			{
				$result = false;
			}
		}
		
		
		
		return $result;
	}
	
	public function addExpenseCategory()
    {
		
		$monthLimitAmount = 0;
		$monthLimitEnabled = 0;
		
		if (isset($this->expenseLimitEnabled))
			 {
				 $monthLimitAmount = $this->limitAmount;
				 $monthLimitEnabled = 1;
			 }
            else
			{
				$monthLimitAmount = 0;
				$monthLimitEnabled = 0;
			}
		
		$sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name, month_limit, month_limit_activated)
                    VALUES (:userID, :name, :month_limit, :month_limit_activated)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':name', $this->newExpenseCategory, PDO::PARAM_STR);
			 $stmt->bindValue(':month_limit', $monthLimitAmount, PDO::PARAM_STR);
			 $stmt->bindValue(':month_limit_activated', $monthLimitEnabled, PDO::PARAM_STR);
            

            return $stmt->execute();
	}
	
	public function updateExpenseCategory()
    {
		$monthLimitAmount = 0;
		$monthLimitEnabled = 0;
		
		if (isset($this->expenseLimitEnabled))
			 {
				 $monthLimitAmount = $this->limitAmount;
				 $monthLimitEnabled = 1;
			 }
            else
			{
				$monthLimitAmount = 0;
				$monthLimitEnabled = 0;
			}
		
		$sql = 'UPDATE expenses_category_assigned_to_users
				SET name = :newName, month_limit = :month_limit, month_limit_activated = :month_limit_activated
				WHERE user_id = :userID AND name = :oldName';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':newName', $this->newExpenseCategory, PDO::PARAM_STR);
			 $stmt->bindValue(':oldName', $this->oldExpenseCategory, PDO::PARAM_STR);
			 $stmt->bindValue(':month_limit', $monthLimitAmount, PDO::PARAM_STR);
			 $stmt->bindValue(':month_limit_activated', $monthLimitEnabled, PDO::PARAM_STR);
			 
			 
			
            return $stmt->execute();
	}
	
	public function deleteExpenseCategory()
    {
		$sql = 'SELECT id FROM expenses_category_assigned_to_users
                    WHERE user_id = :userID AND name = :name';
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
		$stmt->bindValue(':name', $this->methodToDelete, PDO::PARAM_STR);
			
		
		$stmt->execute();
		$categories = $stmt->fetchAll();
		//var_dump($categories);
		$categoryNumber = $categories[0]['id'];
		
		//echo $categories[0]['id'];
		
		
		
		$sql = 'DELETE FROM expenses_category_assigned_to_users
                    WHERE user_id = :userID AND name = :name';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':name', $this->methodToDelete, PDO::PARAM_STR);
            

            $stmt->execute();
			
			$sql = 'DELETE FROM expenses
                    WHERE user_id = :userID AND expense_category_assigned_to_user_id = :expense_category_assigned_to_user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userID', $_SESSION['user_id'], PDO::PARAM_STR);
			 $stmt->bindValue(':expense_category_assigned_to_user_id', $categoryNumber, PDO::PARAM_STR);
			 
		
            

            $stmt->execute();
			
			
	}
}
