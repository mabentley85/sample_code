<?php

class ClsController extends Controller
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
			array('allow',
				'actions'=>array('myClasses'),
				'expression'=>'Yii::app()->user->checkAccess("teacher")',
			),
			array('allow',  // allow Authenticated users to perform 'view' action
				'actions'=>array('view'),
				'expression'=>'Yii::app()->user->checkAccess("viewCls")',
			),
			array('allow',
				'actions'=>array('classDetails'),
				'expression'=>'Yii::app()->user->checkAccess("teacher")',
			),
			array('allow', // allow authenticated user to perform 'create' action
				'actions'=>array('create'),
				'expression'=>'Yii::app()->user->checkAccess("createCls")',
			),
			array('allow', // allow authenticated user to perform 'update' action
				'actions'=>array('update'),
				'expression'=>'Yii::app()->user->checkAccess("updateCls")',
			),
			array('allow', // allow authenticated user to perform 'admin' action
				'actions'=>array('admin'),
				'expression'=>'Yii::app()->user->checkAccess("adminCls")',
			),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('delete'),
				'expression'=>'Yii::app()->user->checkAccess("deleteCls")',
				),
			array('allow', // allow authenticated user to perform 'softDelete' action
				'actions'=>array('softDelete'),
				'expression'=>'Yii::app()->user->checkAccess("deleteCls")',	
				),
			array('allow', // allow authenticated user to perform 'addStudent' action
				'actions'=>array('addStudent'),
				'expression'=>'Yii::app()->user->checkAccess("addStudent")',	
				),
			array('allow', // allow authenticated user to perform 'removeStudent' action
				'actions'=>array('removeStudent'),
				'expression'=>'Yii::app()->user->checkAccess("removeStudent")',	
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
	
	/*
	 * View Class Details
	 */
	
	public function actionClassDetails($id)
		{
			$this->render('classDetails', array('model'=>$this->loadModel($id)));
		}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Cls;

		if(isset($_POST['Cls']))
		{
			$model->attributes=$_POST['Cls'];
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

		if(isset($_POST['Cls']))
		{
			$model->attributes=$_POST['Cls'];
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
		$softDelete = Cls::model()->findByPk($id);
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
	 * Lists all Classes that a user has access to view.
	 */
	public function actionIndex()
	{
		// Pulls Down a list of Classes That are associated with the User that is logged in
		$userId = Yii::app()->user->id;
		
		if(Yii::app()->user->checkAccess("admin"))
		{
			$dataProvider = new CActiveDataProvider('Cls',
				array(
					'criteria'=>array('condition'=>'isDeleted=0'),));	
		}
		elseif(Yii::app()->user->checkAccess("district_admin"))
		{
			// Gets User's District
			$userDistrictId = User::model()->findByPk($userId)->district_id;
			$criteria = new CDbCriteria;
			// Use "Condition" for the 'WHERE' statement in SQL
			$criteria->condition = 'school_id IN (SELECT DISTINCT id FROM `school` WHERE district_id=:districtId)';
			$criteria->params = array(':districtId'=>$userDistrictId);					
			$dataProvider = new CActiveDataProvider('Cls', array('criteria'=>$criteria), true);
		}
		elseif(Yii::app()->user->checkAccess("school_admin"))
		{
			$userSchoolId = User::model()->findByPk($userId)->school_id;
			$criteria = new CDbCriteria;
			$criteria->condition = "isDeleted=0 AND school_id=:userSchoolId";
			$criteria->params = array(':userSchoolId'=>$userSchoolId);	
			$dataProvider = new CActiveDataProvider('Cls', array('criteria'=>$criteria,));	
		}
		elseif(Yii::app()->user->checkAccess("teacher"))// If the User is a Teacher
		{	
			$dataProvider=new CActiveDataProvider('Cls', array('criteria'=>array(
					'condition'=>'isDeleted=0 && user_id=' . Yii::app()->user->id),));
		}
		$this->render('index',array('dataProvider'=>$dataProvider,));
	}

	/*
	 *  MyClasses shows a tab for each day's classes
	 */
	
	public function actionMyClasses()
		{
			$userId = Yii::app()->user->id;
			$userDistrictId = User::model()->findByPk($userId)->district_id;
			$timeZone = District::model()->findByPk($userDistrictId)->timeZone;
			
			if($timeZone == "ET")
				{
					$timeDif = 'America/New_York';
				}
			elseif($timeZone == "CT")
				{
					$timeDif = 'America/Chicago';
				}
			elseif($timeZone == "MT")
				{
					$timeDif = 'America/Denver';
				}
			elseif($timeZone == "PT")
				{
					$timeDif = 'America/Los_Angeles';
				}
			
			// Apply TimeZone Adjustment to Find Current Day
			date_default_timezone_set('America/Denver');
			$currentDay = date("D");
			
			// Set Active Tab Based on Current Day
			if($currentDay == 'Sun' || $currentDay == 'Mon')
				{
					$activeTab = 1;
				}
			elseif($currentDay == 'Tue')
				{
					$activeTab = 2;
				}
			elseif($currentDay == 'Wed')
				{
					$activeTab = 3;
				}
			elseif($currentDay == 'Thu')
				{
					$activeTab = 4;
				}
			elseif($currentDay == 'Fri' || $currentDay == 'Sat')
				{
					$activeTab = 5;
				}
			else
				{
					$activeTab = 1;
				}
				
			// Daily Data Providers
			//Monday
			$monCriteria = new CDbCriteria;
			$monCriteria->alias = 'a';
			$monCriteria->join = 'JOIN `period` `b` ON a.period_id = b.id';
			$monCriteria->condition = 'a.isDeleted=0 AND a.user_id=:userId';
			$monCriteria->params = array(':userId'=>$userId);
			$monCriteria->addInCondition('b.monday_bool', array('1'));
			$monCriteria->order = 'b.period_start_time';
			$monDataProvider=new CActiveDataProvider('Cls', array('criteria'=>$monCriteria), true);

			// Tuesday
			$tueCriteria = new CDbCriteria;
			$tueCriteria->alias = 'a';
			$tueCriteria->join = 'JOIN `period` `b` ON a.period_id = b.id';
			$tueCriteria->condition = 'a.isDeleted=0 AND a.user_id=:userId';
			$tueCriteria->params = array(':userId'=>$userId);
			$tueCriteria->addInCondition('b.tuesday_bool', array('1'));
			$tueCriteria->order = 'b.period_start_time';
			$tueDataProvider=new CActiveDataProvider('Cls', array('criteria'=>$tueCriteria), true);
			
			// Wednesday
			$wedCriteria = new CDbCriteria;
			$wedCriteria->alias = 'a';
			$wedCriteria->join = 'JOIN `period` `b` ON a.period_id = b.id';
			$wedCriteria->condition = 'a.isDeleted=0 AND a.user_id=:userId';
			$wedCriteria->params = array(':userId'=>$userId);
			$wedCriteria->addInCondition('b.wednesday_bool', array('1'));
			$wedCriteria->order = 'b.period_start_time';
			$wedDataProvider=new CActiveDataProvider('Cls', array('criteria'=>$wedCriteria), true);
			
			// Thursday
			$thurCriteria = new CDbCriteria;
			$thurCriteria->alias = 'a';
			$thurCriteria->join = 'JOIN `period` `b` ON a.period_id = b.id';
			$thurCriteria->condition = 'a.isDeleted=0 AND a.user_id=:userId';
			$thurCriteria->params = array(':userId'=>$userId);
			$thurCriteria->addInCondition('b.thursday_bool', array('1'));
			$thurCriteria->order = 'b.period_start_time';
			$thurDataProvider=new CActiveDataProvider('Cls', array('criteria'=>$thurCriteria), true);
			
			// Friday
			$friCriteria = new CDbCriteria;
			$friCriteria->alias = 'a';
			$friCriteria->join = 'JOIN `period` `b` ON a.period_id = b.id';
			$friCriteria->condition = 'a.isDeleted=0 AND a.user_id=:userId';
			$friCriteria->params = array(':userId'=>$userId);
			$friCriteria->addInCondition('b.friday_bool', array('1'));
			$friCriteria->order = 'b.period_start_time';
			$friDataProvider=new CActiveDataProvider('Cls', array('criteria'=>$friCriteria), true);
			
			// Render the view
			$this->render('myClasses', array('monDataProvider'=>$monDataProvider,
											'tueDataProvider'=>$tueDataProvider,
											'wedDataProvider'=>$wedDataProvider,
											'thurDataProvider'=>$thurDataProvider,
											'friDataProvider'=>$friDataProvider,
											'activeTab'=>$activeTab));
		}

	/*
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Cls('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Cls']))
			$model->attributes=$_GET['Cls'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/*
	 *  Method that adds student and class relationship to student_class table
	 */
	public function actionAddStudent()
		{
			$studentId = $_POST['student_id'];
			$classId = $_POST['classId'];
			$studentName = $_POST['term'];
			// Check if student Exists in DB
			if($studentId == null)
				{
					Yii::app()->user->setFlash('error', 'This student does not exist.');
					$this->redirect('' . $classId);
				}
			
			// Check to See if this relationship All ready exists
			$condition = 'student_id=:studentId AND class_id=:classId';
			$params = array(':studentId'=>$studentId, ':classId'=>$classId);
			$check = StudentClass::model()->count($condition,$params);
			if ($check == 0)
				{
					$addStudent = new StudentClass;
					$addStudent->student_id = $studentId;
					$addStudent->class_id = $classId;
					$addStudent->update_user = Yii::app()->user->id;
					$student = Student::model()->findByPk($studentId)->student_first_name . " " . Student::model()->findByPk($studentId)->student_last_name;
					$class = Cls::model()->findByPK($classId)->class_name;
					// Check if Student Belong in school for the class he is being added to
					$studentSchoolId = Student::model()->findByPk($studentId)->school_id;
					$classSchoolId = Cls::model()->findByPk($classId)->school_id;
					if($studentSchoolId == $classSchoolId)
						{
							// Success Message
							$addStudent->save();
							$flashSuccess = $student . " has been added to " . $class . ".<br />";
							Yii::app()->user->setFlash('success', $flashSuccess);
							$this->redirect('' . $classId);
						}
					else
						{
							// Redirect with a flash-error
							$flashError = $student . " could not be added to " . $class . ".<br />They do not attend this school.";	
							Yii::app()->user->setFlash('error', $flashError);
							$this->redirect('' . $classId);
						}					
				}
			
			$this->redirect('' . $classId);
		}
	
	/*
	 * Method that removes student from a class
	 */
	public function actionRemoveStudent()
		{
			//echo "Remove Student Action!!<br />";	
			$studentId = $_GET['student_id'];
			$classId = $_GET['class_id'];
			//echo "Student ID is: " . $studentId . "<br />";
			//echo "Class ID is: " . $classId . "<br />";
			StudentClass::model()->deleteAll('student_id=:studentId AND class_id=:classId', array(':studentId'=>$studentId, ':classId'=>$classId));
			//$this->render('view',array('model'=>$this->loadModel($classId),true));
			$this->redirect(array('view','id'=>$classId));
			
		}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Cls::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='cls-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}	

}