<?php

class ContactController extends Controller
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
				'expression'=>'Yii::app()->user->checkAccess("viewContact")',
			),
			array('allow', // allow authenticated user to perform 'create' action
				'actions'=>array('create'),
				'expression'=>'Yii::app()->user->checkAccess("createContact")',
			),
			array('allow', // allow authenticated user to perform 'update' action
				'actions'=>array('update'),
				'expression'=>'Yii::app()->user->checkAccess("updateContact")',
			),
			array('allow', // allow authenticated user to perform 'admin' action
				'actions'=>array('admin'),
				'expression'=>'Yii::app()->user->checkAccess("adminContact")',
			),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('delete'),
				'expression'=>'Yii::app()->user->checkAccess("deleteContact")',
				),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('contactList'),
				'expression'=>'Yii::app()->user->checkAccess("addStudentContact")',
				),
			array('allow', // allow authenticated user to perform 'softDelete' action
				'actions'=>array('softDelete'),
				'expression'=>'Yii::app()->user->checkAccess("deleteCls")',	
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
		$model=new Contact;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Contact']))
		{
			$model->attributes = $_POST['Contact'];
			// Before Save Format Phone number is Voice or Text is Selected Then Validate
			if($model->method == 'text' || $model->method == 'voice')
			{
				$model->data = preg_replace("/[^0-9]/", "", $model->data);
				if(strlen($model->data) != 10)
					{
						Yii::app()->user->setFlash('error', 'Phone number needs to be ten digits');
						$this->redirect(array('create'));
					}
				
			}
			elseif($model->method == 'email')
			{
					
				if(!self::validateEmail($model->data))
					{
						Yii::app()->user->setFlash('error', 'The email address you entered is not valid');
						$this->redirect(array('create'));
					}
				
			}
						
			if($model->save())
				{
					// Create New Entry in Student_Contact Table
					$studentId = $_POST['Contact']['student_id'];
					$contactId = $model->id;
					//print "Contact ID: " . $contactId;
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
						}
											
					$this->redirect(array('view','id'=>$model->id));
					
				}
			 
		}

		$this->render('create',array('model'=>$model,));
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

		if(isset($_POST['Contact']))
		{
			$model->attributes=$_POST['Contact'];
			// Validate Update
			// Before Save Format Phone number is Voice or Text is Selected Then Validate
			if($model->method == 'text' || $model->method == 'voice')
			{
				$model->data = preg_replace("/[^0-9]/", "", $model->data);
				if(strlen($model->data) != 10)
					{
						Yii::app()->user->setFlash('error', 'Phone number needs to be ten digits');
						$this->redirect(array('create'));
					}
				
			}
			elseif($model->method == 'email')
			{
					
				if(!self::validateEmail($model->data))
					{
						Yii::app()->user->setFlash('error', 'The email address you entered is not valid');
						$this->redirect(array('create'));
					}
				
			}
			// End of Validation
			
			if($model->save())
				{
					$this->redirect(array('view','id'=>$model->id));
				}
		}

		$this->render('update',array('model'=>$model,));
	}
	
	/*
	 * Soft Deleted updates the the isDeleted column in the table for the specific entry
	 */
	public function actionSoftDelete($id)
	{
		$softDelete = Contact::model()->findByPk($id);
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
			
		if(Yii::app()->user->checkAccess("admin"))
		{
			$criteria = new CDbCriteria;
			$criteria->condition = 'isDeleted=0';
			$criteria->order = 'contact_name';
			$dataProvider=new CActiveDataProvider('Contact', array('criteria'=>$criteria));
		}
		elseif(Yii::app()->user->checkAccess("district_admin"))
		{
			// Creates a Simple array of School IDs of schools in the User's District
			$schoolIdList = Yii::app()->db->createCommand("SELECT DISTINCT id FROM `school` WHERE district_id = {$userDistrictId}")->queryColumn();
			$criteria = new CDbCriteria;
			$criteria->condition = 'isDeleted=0';
			// Queries for all rows where the 'school_id' value matches the values in the $schoolIdList array
			$criteria->addInCondition('school_id', $schoolIdList);
			$criteria->order = 'contact_name';
			$dataProvider=new CActiveDataProvider('Contact', array('criteria'=>$criteria));
		}
		elseif(Yii::app()->user->checkAccess("school_admin"))
		{
			$criteria = new CDbCriteria;
			$criteria->condition = 'isDeleted=0 AND school_id=:userSchoolId';
			$criteria->params = array(':userSchoolId'=>$userSchoolId);
			$criteria->order = 'contact_name';
			$dataProvider=new CActiveDataProvider('Contact', array('criteria'=>$criteria));
		}
		elseif(Yii::app()->user->checkAccess("teacher"))
		{
			$studentIdList = Yii::app()->db->createCommand("SELECT DISTINCT student_id FROM `student_class` WHERE class_id IN (SELECT id FROM cls WHERE user_id = {$userId});")->queryColumn();
			$criteria = new CDbCriteria;
			$criteria->condition = 'isDeleted=0';
			$criteria->addInCondition('student_id', $studentIdList);
			$criteria->order = 'contact_name';
			$dataProvider=new CActiveDataProvider('Contact', array('criteria'=>$criteria));
		}
		
		$this->render('index',array('dataProvider'=>$dataProvider,));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Contact('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Contact']))
			$model->attributes=$_GET['Contact'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/*
	 *  Creates a List of Contacts for AutoComplete
	 */
	public function actionContactList()
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
				// TO DO: Make sure only contacts for a school 
				if(Yii::app()->user->checkAccess('admin'))
					{
						$qtxt = "SELECT contact_name as `label`, `id` as `id`, contact_name  as `value` FROM contact WHERE contact_name LIKE :contactName";
					}
				elseif(Yii::app()->user->checkAccess('district_admin'))
					{
						$qtxt = "SELECT contact_name as `label`, `id` as `id`, contact_name  as `value` FROM contact WHERE contact_name LIKE :contactName 
						AND school_id IN (SELECT DISTINCT school_id FROM `school` WHERE district_id = {$userDistrictId})";
						
					}
				elseif(Yii::app()->user->checkAccess('teacher'))
					{
						$qtxt = "SELECT contact_name as `label`, `id` as `id`, contact_name  as `value` FROM contact WHERE contact_name LIKE :contactName AND school_id = {$userSchoolId}";
					}
				$command = Yii::app()->db->createCommand($qtxt);
				$command->bindValue(":contactName", '%'.$term.'%', PDO::PARAM_STR);
				$res = $command->queryAll();
			}
			echo CJSON::encode($res);
			Yii::app()->end();
			
			
		}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Contact::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='contact-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function validateEmail($email)
		{
			$validator = new CEmailValidator;
			if($validator->validateValue($email))
				{
					return true;
				}
			else
				{
					return false;
				}
		}
			
			
}
