<?php

namespace App\Models;
use PDO;

class Balances extends \Core\Model
{
    public $errors = [];
	public $incomes;
	public $expenses;
	public $expensesToChart;
	public $sumOfIncomes = "";
	public $sumOfExpenses = "";
	public $periodSelect = 1;
	
	public $beginDate = "";
	public $endDate = "";
	 
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	 
	 private $thisMonthIncomesQuery = "SELECT incomes.id, incomes.user_id, incomes_category_assigned_to_users.name, incomes.amount, incomes.date_of_income, incomes.income_comment FROM incomes LEFT JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id WHERE incomes.user_id=:userID AND Year(date_of_income) = Year(Now()) And Month(date_of_income) = Month(Now())";
	
	 private $thisMonthExpensesQuery = "SELECT expenses.id, expenses.user_id, expenses_category_assigned_to_users.name, expenses.amount, expenses.date_of_expense, expenses.expense_comment FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id=:userID AND Year(date_of_expense) = Year(Now()) And Month(date_of_expense) = Month(Now())";
	 
	 private $lastMonthIncomesQuery = "SELECT incomes.id, incomes.user_id, incomes_category_assigned_to_users.name, incomes.amount, incomes.date_of_income, incomes.income_comment FROM incomes LEFT JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id WHERE incomes.user_id=:userID AND YEAR(date_of_income) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(date_of_income) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
	 
	 private $lastMonthExpensesQuery = "SELECT expenses.id, expenses.user_id, expenses_category_assigned_to_users.name, expenses.amount, expenses.date_of_expense, expenses.expense_comment FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id=:userID AND YEAR(date_of_expense) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(date_of_expense) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
	 
	 private $currentYearIncomesQuery = "SELECT incomes.id, incomes.user_id, incomes_category_assigned_to_users.name, incomes.amount, incomes.date_of_income, incomes.income_comment FROM incomes LEFT JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id WHERE incomes.user_id=:userID AND YEAR(date_of_income) = YEAR(CURDATE())";
	 
	 private $currentYearExpensesQuery = "SELECT expenses.id, expenses.user_id, expenses_category_assigned_to_users.name, expenses.amount, expenses.date_of_expense, expenses.expense_comment FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id=:userID AND YEAR(date_of_expense) = YEAR(CURDATE())";
	 
	 private $customDateIncomesQuery = "SELECT incomes.id, incomes.user_id, incomes_category_assigned_to_users.name, incomes.amount, incomes.date_of_income, incomes.income_comment FROM incomes LEFT JOIN incomes_category_assigned_to_users ON incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id WHERE incomes.user_id=:userID AND date_of_income BETWEEN :beginDate AND :endDate";
	 
	 private $customDateExpensesQuery = "SELECT expenses.id, expenses.user_id, expenses_category_assigned_to_users.name, expenses.amount, expenses.date_of_expense, expenses.expense_comment FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id=:userID AND date_of_expense BETWEEN :beginDate AND :endDate";
	 
	 
	 
	 private $thisMonthExpensesChartQuery = "SELECT expenses_category_assigned_to_users.name, SUM(amount) FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id=:userID AND YEAR(date_of_expense) = Year(Now()) And Month(date_of_expense) = Month(Now()) GROUP BY expense_category_assigned_to_user_id";
	 
	 private $lastMonthExpensesChartQuery = "SELECT expenses_category_assigned_to_users.name, SUM(amount) FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id=:userID AND YEAR(date_of_expense) = Year(Now()) And Month(date_of_expense) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) GROUP BY expense_category_assigned_to_user_id";
	 
	 private $currentYearExpensesChartQuery = "SELECT expenses_category_assigned_to_users.name, SUM(amount) FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id=:userID AND YEAR(date_of_expense) = YEAR(CURDATE()) GROUP BY expense_category_assigned_to_user_id";
	 
	 private $customDateExpensesChartQuery = "SELECT expenses_category_assigned_to_users.name, SUM(amount) FROM expenses LEFT JOIN expenses_category_assigned_to_users ON expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id WHERE expenses.user_id=:userID AND date_of_expense BETWEEN :beginDate AND :endDate GROUP BY expense_category_assigned_to_user_id";
	 
	
	
	public function getIncomesFromDB($sql)
    { 
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
		if	($sql == $this->customDateIncomesQuery)
			{
				$stmt->bindValue(':beginDate', $this->beginDate, PDO::PARAM_STR);
				$stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
			}
		
		$stmt->execute();
		$incomes = $stmt->fetchAll();
		
		return $incomes;
	}
	
	public function getExpensesFromDB($sql)
    {
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
		if	($sql == $this->customDateExpensesQuery)
			{
				$stmt->bindValue(':beginDate', $this->beginDate, PDO::PARAM_STR);
				$stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
			}
		
		$stmt->execute();
		$expenses = $stmt->fetchAll();
		
		return $expenses;
	}
	
	public function getExpensesFromDBToChart($sql)
    {
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->bindValue(':userID', \App\Auth::getUserID(), PDO::PARAM_STR);
		if	($sql == $this->customDateExpensesChartQuery)
			{
				$stmt->bindValue(':beginDate', $this->beginDate, PDO::PARAM_STR);
				$stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
			}
		
		$stmt->execute();
		$expensesToChart = $stmt->fetchAll();
		
		return $expensesToChart;
	}
	
	public function displayDataFromSelectedPeriod()
    {
		switch ($this->periodSelect) 
		{
    case 1:
        $this->incomes = $this->getIncomesFromDB($this->thisMonthIncomesQuery);
		$this->expenses = $this->getExpensesFromDB($this->thisMonthExpensesQuery);
		$this->expensesToChart = $this->getExpensesFromDBToChart($this->thisMonthExpensesChartQuery);
        break;
    case 2:
        $this->incomes = $this->getIncomesFromDB($this->lastMonthIncomesQuery);
		$this->expenses = $this->getExpensesFromDB($this->lastMonthExpensesQuery);
		$this->expensesToChart = $this->getExpensesFromDBToChart($this->lastMonthExpensesChartQuery);
        break;
    case 3:
        $this->incomes = $this->getIncomesFromDB($this->currentYearIncomesQuery);
		$this->expenses = $this->getExpensesFromDB($this->currentYearExpensesQuery);
		$this->expensesToChart = $this->getExpensesFromDBToChart($this->currentYearExpensesChartQuery);
        break;
	case 4:
        $this->incomes = $this->getIncomesFromDB($this->customDateIncomesQuery);
		$this->expenses = $this->getExpensesFromDB($this->customDateExpensesQuery);
		$this->expensesToChart = $this->getExpensesFromDBToChart($this->customDateExpensesChartQuery);
        break;
		}
	}
	
	
	public function sumOfIncomes()
    {
        $this->sumOfIncomes = 0;
		foreach ($this->incomes as $item) 
		{
			$this->sumOfIncomes += $item['amount'];
		}
    }
	
	public function sumOfExpenses()
    {
        $this->sumOfExpenses = 0;
		foreach ($this->expenses as $item) 
		{
			$this->sumOfExpenses += $item['amount'];
		}
    }
	
	public static function addSumOfIncomes($sum)
    {
        $_SESSION['sumOfIncomes'] = $sum;
    }
	
	public static function addSumOfExpenses($sum)
    {
        $_SESSION['sumOfExpenses'] = $sum;
    }

    public static function getSumOfIncomes()
    {
        if (isset($_SESSION['sumOfIncomes'])) {
            
            $messages = $_SESSION['sumOfIncomes'];
            unset($_SESSION['sumOfIncomes']);

            return $messages;
        }
    }
	
	public static function getSumOfExpenses()
    {
        if (isset($_SESSION['sumOfExpenses'])) {
            
            $messages = $_SESSION['sumOfExpenses'];
            unset($_SESSION['sumOfExpenses']);

            return $messages;
        }
    }
	
	public static function addExpenseToChart($message)
    {
        $_SESSION['expenseToChart'] = $message;
    }
	
	public static function getExpenseToChart()
    {
        if (isset($_SESSION['expenseToChart'])) {
            
            $messages = $_SESSION['expenseToChart'];
            unset($_SESSION['expenseToChart']);

            return $messages;
        }
    }
	
	public static function addIncome($message)
    {
        $_SESSION['income'] = $message;
    }
	
	public static function addExpense($message)
    {
        $_SESSION['expense'] = $message;
    }

    public static function getIncome()
    {
        if (isset($_SESSION['income'])) {
            
            $messages = $_SESSION['income'];
            unset($_SESSION['income']);

            return $messages;
        }
    }
	
	public static function getExpense()
    {
        if (isset($_SESSION['expense'])) {
            
            $messages = $_SESSION['expense'];
            unset($_SESSION['expense']);

            return $messages;
        }
    }
	
	public static function sendSelected($sum)
    {
        $_SESSION['selected'] = $sum;
    }
     
	 
	  public static function getSelected()
    {
        if (isset($_SESSION['selected'])) {
            
            $messages = $_SESSION['selected'];
            unset($_SESSION['selected']);

            return $messages;
        }
    }
}
