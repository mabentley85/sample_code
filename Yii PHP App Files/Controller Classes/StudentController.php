<?php

class StudentController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('index'),
				'users'=>array('@'),
			),
			array('allow',  // allow Authenticated users to perform 'view' action
				'actions'=>array('view'),
				'expression'=>'Yii::app()->user->checkAccess("viewStudent")',
			),
			array('allow', // allow authenticated user to perform 'create' action
				'actions'=>array('create'),
				'expression'=>'Yii::app()->user->checkAccess("createStudent")',
			),
			array('allow', // allow authenticated user to perform 'update' action
				'actions'=>array('update'),
				'expression'=>'Yii::app()->user->checkAccess("updateStudent")',
			),
			array('allow', // allow authenticated user to perform 'admin' action
				'actions'=>array('admin'),
				'expression'=>'Yii::app()->user->checkAccess("adminStudent")',
			),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('delete'),
				'expression'=>'Yii::app()->user->checkAccess("deleteStudent")',
				),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('softDelete'),
				'expression'=>'Yii::app()->user->checkAccess("deleteStudent")',
				),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('addStudentContact'),
				'expression'=>'Yii::app()->user->checkAccess("addStudentContact")',
				),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('removeStudentContact'),
				'expression'=>'Yii::app()->user->checkAccess("addStudentContact")',
				),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('list'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model= new Student;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Student']))
		{
			$model->attributes=$_POST['Student'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Student']))
		{
			$model->attributes=$_POST['Student'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	/*
	 * Soft Deleted updates the the isDeleted column in the table for the specific entry
	 */
	public function actionSoftDelete($id)
	{
		$softDelete = Student::model()->findByPk($id);
		if (!$softDelete->isDeleted == 1)
		{
			$softDelete->isDeleted = 1;
			$softDelete->save();
			self::actionIndex();
		}
		
		else 
		{
			self::actionIndex();
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$userId = Yii::app()->user->id;
		$userDistrictId = User::model()->findByPk($userId)->district_id;	
		$userSchoolId = User::model()->findByPk($userId)->school_id;
		
		if(Yii::app()->user->checkAccess('admin'))
			{
				$criteria = new CDbCriteria;
				$criteria->condition = 'isDeleted=0';
				$criteria->order = 'student_last_name';
				$dataProvider = new CActiveDataProvider('Student', array('criteria'=>$criteria, 'pagination'=>array('pageSize'=>35),));
			}
		elseif(Yii::app()->user->checkAccess('district_admin'))
			{
				// Creates a Simple array of School IDs of schools in the User's District
				$schoolIdList = Yii::app()->db->createCommand("SELECT DISTINCT id FROM `school` WHERE district_id = {$userDistrictId}")->queryColumn();
				$criteria = new CDbCriteria;
				$criteria->condition = 'isDeleted=0';
				// Queries for all rows where the 'school_id' value matches the values in the $schoolIdList array
				$criteria->addInCondition('school_id', $schoolIdList);
				$criteria->order = 'student_last_name';
				$dataProvider = new CActiveDataProvider('Student', array('criteria'=>$criteria, 'pagination'=>array('pageSize'=>35),));
			}
		elseif(Yii::app()->user->checkAccess('school_admin'))
			{
				$criteria = new CDbCriteria;
				$criteria->condition = 'isDeleted=0 AND school_id=:userSchoolId';
				$criteria->params = array(':userSchoolId'=>$userSchoolId);
				$criteria->order = 'student_last_name';
				$dataProvider = new CActiveDataProvider('Student', array('criteria'=>$criteria, 'pagination'=>array('pageSize'=>35),));
				
			}
		elseif(Yii::app()->user->checkAccess('teacher'))
			{
				// Creates simple array of student ID's who are in a particular teacher's class
				$studentIdList = Yii::app()->db->createCommand("SELECT DISTINCT student_id FROM `student_class` WHERE class_id IN (SELECT id FROM cls WHERE user_id = {$userId});")->queryColumn();
				$criteria = new CDbCriteria;
				$criteria->condition = 'id';
				// Queries for all rows in where the 'id' column value matches the values in the $studentList array
				$criteria->addInCondition('id', $studentIdList);
				$criteria->order = 'student_last_name';
				$dataProvider = new CActiveDataProvider('Student', array('criteria'=>$criteria, 'pagination'=>array('pageSize'=>35),));
			}
			
		$this->render('index',array('dataProvider'=>$dataProvider,));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Student('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Student']))
			$model->attributes=$_GET['Student'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionList()
		{
			$userId = Yii::app()->user->id;
			$userSchoolId = User::model()->findByPk($userId)->school_id;
			$userDistrictId = User::model()->findByPk($userId)->district_id;
				
			$res = array();
			// Create an Array of Student First and Last Names Keyed by Student ID number
			// Add ID and Student Name to a hidden field, cross reference if there is no ID in the hidden field before
			if (isset($_GET['term'])) 
			{
				/*
				 * SQL Syntax Way - http://www.yiiframework.com/doc/guide/database.dao
				 */
				$term = $_GET['term'];
				// TO DO: Make sure autocomplete only searches for school user has access to.
				if(Yii::app()->user->checkAccess('admin'))
					{
						$qtxt = "SELECT CONCAT_WS(' ',`student_first_name`, `student_last_name`) as `label`,
						 `id` as `id`, CONCAT_WS(' ',`student_first_name`, `student_last_name`)  as `value`
						  FROM student WHERE (CONCAT_WS(' ',`student_first_name`, `student_last_name`) LIKE :studentName)";
					}
				elseif(Yii::app()->user->checkAccess('district_admin'))
					{
						$qtxt = "SELECT CONCAT_WS(' ',`student_first_name`, `student_last_name`) as `label`,
						 `id` as `id`, CONCAT_WS(' ',`student_first_name`, `student_last_name`)  as `value`
						  FROM student WHERE (CONCAT_WS(' ',`student_first_name`, `student_last_name`) LIKE :studentName) 
						  AND school_id IN (SELECT DISTINCT school_id FROM `school` WHERE district_id = {$userDistrictId})";
					}
				elseif(Yii::app()->user->checkAccess('teacher'))
					{
						$qtxt = "SELECT CONCAT_WS(' ',`student_first_name`, `student_last_name`) as `label`,
						 `id` as `id`, CONCAT_WS(' ',`student_first_name`, `student_last_name`)  as `value`
						  FROM student WHERE (CONCAT_WS(' ',`student_first_name`, `student_last_name`) LIKE :studentName) AND school_id={$userSchoolId}";
					}
				$command = Yii::app()->db->createCommand($qtxt);
				$command->bindValue(":studentName", '%'.$term.'%', PDO::PARAM_STR);
				$res = $command->queryAll();
			}
			echo CJSON::encode($res);
			Yii::app()->end();
		}
		
		/*
		 * Attaches a contact to a student
		 */
		
	public function actionAddStudentContact()
		{
			$studentId = $_POST['student_id'];
			$contactId = $_POST['contact_id'];
			$studentName = $_POST['term'];
			// Check to Make sure the Contact Exists
			if($contactId == null)
				{
					Yii::app()->user->setFlash('error', 'This contact does not exist.');
					$this->redirect('' . $studentId);
				}
			// Check to make sure the relationship doesn't all ready exist
			$condition = 'student_id=:studentId AND contact_id=:contactId';
			$params = array(':studentId'=>$studentId, ':contactId'=>$contactId);
			$check = StudentContact::model()->count($condition,$params);
			if ($check == 0)
				{
					$addStudentContact = new StudentContact;
					$addStudentContact->student_id = $studentId;
					$addStudentContact->contact_id = $contactId;
					$addStudentContact->update_user = Yii::app()->user->id;
					$addStudentContact->save();
					Yii::app()->user->setFlash('success', Contact::model()->findByPk($contactId)->contact_name . 
						' was added as a contact for ' . Student::model()->findByPk($studentId)->student_first_name . ' ' 
						. Student::model()->findByPk($studentId)->student_last_name . '.' );
					$this->redirect('' . $studentId);
				}
			
			Yii::app()->user->setFlash('error', Contact::model()->findByPk($contactId)->contact_name . 
						' could not be added as a contact for ' . Student::model()->findByPk($studentId)->student_first_name . ' ' 
						. Student::model()->findByPk($studentId)->student_last_name . '.' );
			$this->redirect('' . $studentId);			
		}
		
	/*
	 * Removes a Contact from a Student
	 */
	public function actionRemoveStudentContact()
		{
			//echo "Remove Student Action!!<br />";	
			$contactId = $_GET['contact_id'];
			$studentId = $_GET['student_id'];
			//echo "Student ID is: " . $studentId . "<br />";
			//echo "Contact ID is: " . $contactId . "<br />";
			StudentContact::model()->deleteAll('student_id=:studentId AND contact_id=:contactId', array(':studentId'=>$studentId, ':contactId'=>$contactId));
			// Check to See if There are any more contacts for this student 
			//$rows = StudentContact::model()->count('student_id=:studentId', array(':studentId'=>$studentId));
			//echo "Rows: " . $rows . "<br />";
			$this->render('view',array('model'=>$this->loadModel($studentId)));
						
		}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	
	public function loadModel($id)
	{
		$model=Student::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/*
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='student-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}	
}