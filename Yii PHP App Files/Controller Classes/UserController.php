<?php

class UserController extends Controller
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
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'view' action
				'actions'=>array('viewSelf'),
				'users' => array('@'),
			),			
			array('allow',  // allow Authenticated users to perform 'view' action
				'actions'=>array('view'),
				'expression'=>'Yii::app()->user->checkAccess("viewUser")',
			),
			array('allow',  // allow Authenticated users to perform 'viewTeacher' action
				'actions'=>array('viewTeacher'),
				'expression'=>'Yii::app()->user->checkAccess("viewTeacher")',
			),
			array('allow', // allow authenticated user to perform 'create' action
				'actions'=>array('create'),
				'expression'=>'Yii::app()->user->checkAccess("createUser")',
			),
			array('allow', // allow authenticated user to perform 'update' action
				'actions'=>array('update'),
				'expression'=>'Yii::app()->user->checkAccess("updateUser")',
			),
			array('allow', // allow authenticated user to perform 'updateSelf' action
				'actions'=>array('updateSelf'),
				'expression'=>'Yii::app()->user->checkAccess("updateSelf")',
			),
			array('allow', // allows user to perform 'pwChange' action
				'actions'=>array('pwChange'),
				'expression'=>'Yii::app()->user->checkAccess("teacher")',
			),
			array('allow', // allow authenticated user to perform 'admin' action
				'actions'=>array('admin'),
				'expression'=>'Yii::app()->user->checkAccess("adminUser")',
			),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('delete'),
				'expression'=>'Yii::app()->user->checkAccess("deleteUser")',
				),
			array('allow', // allow authenticated user to perform 'softDelete' action
				'actions'=>array('softDelete'),
				'expression'=>'Yii::app()->user->checkAccess("deleteUser")',
				),
			array('allow', // allows user to perform 'viewSchoolAdmin' action
				'actions'=>array('viewSchoolAdmin'),
				'expression'=>'Yii::app()->user->checkAccess("school_admin")',
				),
			array('allow', // allows user to send reset password info
					'actions'=>array('resetPassSend'),
					'users'=>array('*'),
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
		$this->render('view',array('model'=>$this->loadModel($id),));
	}
	
	/*
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionViewSelf()
	{
		$id = Yii::app()->user->id;
		$this->render('view',array('model'=>$this->loadModel($id),));
	}
	
	/*
	 * Displays a list of teachers at School 
	 */
	public function actionViewTeacher()
	{
		if (Yii::app()->user->checkAccess('school_admin'))
		{
			$schoolId = User::model()->findByPk(Yii::app()->user->id)->school_id;
			$dataProvider = new CActiveDataProvider('User', 
												array('criteria'=>array(
													'condition'=>'school_id=' . $schoolId . ' AND role="teacher" AND isDeleted=0')));
		}
		
		$this->render('index', array('dataProvider'=>$dataProvider));
	}
	
	/*
	 * Displays a List of school admins
	 */
	public function actionViewSchoolAdmin()
	{
		if(Yii::app()->user->checkAccess('district_admin'))
		{
			$districtId = User::model()->findByPk(Yii::app()->user->id)->district_id;
			$dataProvider = new CActiveDataProvider('User', 
													array('criteria'=>array(
													'condition'=>'district_id=' . $districtId . ' AND role="school_admin" AND isDeleted=0')));	
		}
				
		elseif(Yii::app()->user->checkAccess('school_admin'))
		{
			$schoolId = User::model()->findByPk(Yii::app()->user->id)->school_id;
			$dataProvider = new CActiveDataProvider('User', 
													array('criteria'=>array(
													'condition'=>'school_id=' . $schoolId . ' AND role="school_admin" AND isDeleted=0')));
		}
		
		$this->render('index', array('dataProvider'=>$dataProvider));
	}
	
		
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;
				
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$pass = $model->password;
			if($model->save())
			{
				// Add new user to AuthAssignment table
				Yii::app()->authManager->assign($model->role, $model->id);
				
				$email = $model->username;
				$contactName = $model->user_first_name . ' ' . $model->user_last_name;
				$body = 'Hi ' . $model->user_first_name . ',

Your TruantToday user account has just been created and is ready to use.
We recommend you login to www.TruantToday.com as soon as possible to change you password from the default to one that your create.

Instructions:
1.	Open your web browser and go to www.TruantToday.com.
2.	Click the Login button and your credentials:
		Username: ' . $model->username . '
		Password: ' . $pass . '
3.	After successfully logging in click the My Profile Button, then click the Change My Password button.
4.	In the Change Your Password form enter your given password and then your new password.

Thanks,

-The TruantToday Team';
				
				// Send Email to New User Informing them of their Username and Password
				$message = new YiiMailMessage();
				$message->setTo(array($email=>$contactName));
				$message->setFrom(array('info@truanttoday.com'=>'TruantToday'));
				$message->setSubject('TruantToday - Account Information');
				$message->setBody($body);
				if(Yii::app()->mail->send($message))
					{
						Yii::app()->user->setFlash('success', 'New User Account Created for ' . $model->username);
						$this->redirect(array('view','id'=>$model->id));
					}
				
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
		//$id = Yii::app()->user->id;
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
			{
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array('model'=>$model,));
	}
	/*
	 * This Allows Users to only Update their own Info
	 */
	public function actionUpdateSelf()
		{
			$id = Yii::app()->user->id;
			$model=$this->loadModel($id);

			if(isset($_POST['User']))
				{
					$model->attributes=$_POST['User'];
					if($model->save())
						{
							$this->redirect('viewSelf');							
						}
				}
			
			$this->render('update',array('model'=>$model,));
		}
		
		
	public function actionPwChange()
		{
			$id = Yii::app()->user->id;
			$model = $this->loadModel($id);
			$model->password = '';
			
			if(isset($_POST['User']))
				{
					$user = User::model()->findByPk($id);
					$model->attributes=$_POST['User'];
					if($user->password == md5($model->password))
						{
							if($model->new_password == $model->new_password_repeat)
								{
									$model->password = $model->new_password;
									if($model->save())
										{
											// Redirect with Success Flash
											Yii::app()->user->setFlash('success', 'Password Successfully Changed');
											$this->redirect(array('viewSelf'));
										}
									
								}
							else
								{
									// Redirect with Error Flash for Both New Password do not Match
									Yii::app()->user->setFlash('error', 'Both New Password Fields Must Be Identical');
									$this->redirect('pwChange');
								}
						}
					else
						{
							// Redirect with Error Flash for Incorect Old Password
							Yii::app()->user->setFlash('error', 'Current Password Was Entered Incorrectly');
							$this->redirect('pwChange');
						}
				}
			
			$this->render('pwChange',array('model'=>$model,));
			
		}
	
	/*
	 *  Password Reset, and emails the user a temporary link
	 */
	public function actionResetPassSend()
	{
		$username = $_GET['username'];
		$firstname = $_GET['firstname'];
		$lastname = $_GET['lastname'];
			
		/* Find User model Row where $username exists, if none found Flash Error */
		$userNameResult = User::model()->resetScope()->findBySql('SELECT id FROM `user` WHERE username="' . $username . '";');
		if(count($userNameResult) == 1)
			{
				$userResult = User::model()->resetScope()->findBySql('SELECT id FROM `user` WHERE username="' . $username . '" AND user_first_name="' . $firstname . '" AND user_last_name="' . $lastname . '";');
				if(count($userResult) == 1)
					{
						/*
						 * After everything passes generate hash, add it to TempPw column in User Table 
						 * Then Use that TempPW hash in in email and send it
						 */
						
						// Generate Hash
						$randomString = self::get_random_string(16);
						$userId = $userResult->id;
						$email = User::model()->resetScope()->findByPk($userId)->username;
						$contactName = User::model()->resetScope()->findByPk($userId)->user_first_name . " " . User::model()->resetScope()->findByPk($userId)->user_last_name;
						$model = User::model()->resetScope()->findByPk((int)$userId);
						$model->password = $randomString;
						$model->save();
						$body = 'Please Click the Following Link to Reset your Password is: ' . $randomString . "\nPlease login and change it as soon as possible.\nTo Change your password Click \"My Profile\" at the top of the page, then \"Change Your Password\" on right side menu.";
						
						// Send Email Message to User
						$message = new YiiMailMessage();
						$message->setTo(array($email=>$contactName));
						$message->setFrom(array('info@truanttoday.com'=>'TruantToday'));
						$message->setSubject('TruantToday - New Password Change Link');
						$message->setBody($body);
						if(Yii::app()->mail->send($message))
							{
								Yii::app()->user->setFlash('success', 'Password Successfully Reset<br /> Please Check your email for further instructions.');
								$this->redirect(array('site/pwReset'));
							}
					}
				else
					{
						// Flash Error record was not found
						Yii::app()->user->setFlash('error', 'Username did not match First and Last Name');
						$this->redirect(array('site/pwReset'));
					}
				}
		else
			{
				Yii::app()->user->setFlash('error', 'That email address is not in our records');
				$this->redirect(array('site/pwReset'));
			}
			
		
		
	}
	
	/*
	 * Soft Deleted updates the the isDeleted column in the table for the specific entry
	 */
	public function actionSoftDelete($id)
	{
		$softDelete = User::model()->findByPk($id);
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
		$dataProvider=new CActiveDataProvider('User',
			array('criteria'=>array('condition'=>'isDeleted=0'),));
			
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=User::model()->userScope()->findByPk((int)$id);
		if($model===null)
		{
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/*
	 * Random Alpha Numeric String Generator
	 */
	public function get_random_string($length)
		{
			// start with an empty random string
			$random_string = "";
			
			// Valid Characters
			$valid_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
			
			// count the number of chars in the valid chars string so we know how many choices we have
			$num_valid_chars = strlen($valid_chars);
			 
			// repeat the steps until we've created a string of the right length
			for ($i = 0; $i < $length; $i++)
				{
					// pick a random number from 1 up to the number of valid chars
			 		$random_pick = mt_rand(1, $num_valid_chars);
					
					// take the random character out of the string of valid chars
					// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
					$random_char = $valid_chars[$random_pick-1];
					
					// add the randomly-chosen char onto the end of our string so far
					$random_string .= $random_char;
				}
			
			// return our finished random string
			return $random_string;
		}
	 
}