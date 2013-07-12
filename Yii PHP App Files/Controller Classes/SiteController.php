<?php

class SiteController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		if(Yii::app()->theme->getName() == 'truanttoday')
			{
				$this->layout = 'landingPage';
				$model = new LoginForm;
			}
			
		// Login Functions //
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
			{
				echo CActiveForm::validate($model);
				Yii::app()->end();
			}
		
		// collect user input data
		if(isset($_POST['LoginForm']))
			{
				$model->attributes=$_POST['LoginForm'];
				// validate user input and redirect to the previous page if valid
				if($model->validate() && $model->login())
					{
						if(Yii::app()->user->checkAccess('school_admin'))
							{
								$this->redirect(Yii::app()->request->baseUrl . '/index.php/school/index');
							}
						elseif(Yii::app()->user->checkAccess('teacher'))
							{
								// TO DO: Reference the Current Time and Find the Current Period
								$userId = Yii::app()->user->id;
								
								// Turn Off Scope?
								$userSchoolId = User::model()->findByPk($userId)->school_id;
								$userDistrictId = User::model()->findByPk($userId)->district_id;
								$timeZone = District::model()->findByPk($userDistrictId)->timeZone;
								// Set Local Time Zone
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
								date_default_timezone_set($timeDif);
								$currentTime = date('H:i:s');
																
								// New Class Finder
								$currentClass = Yii::app()->db->createCommand('SELECT id FROM `cls` WHERE period_id IN 
								(SELECT id FROM `period` WHERE (period_start_time <= "' . $currentTime . '" AND period_end_time >= 
								"' . $currentTime . '") AND school_id=' . $userSchoolId . ');')->queryColumn();
								
								//print_r($currentClass);
								if(count($currentClass) == 1)
									{
										$currentClassId = $currentClass[0];
										
										//print "Current Period ID: " . $currentPeriodId . "<br />";
										$this->redirect(Yii::app()->request->baseUrl . '/index.php/cls/' . $currentClassId);
									}
								else
									{
										$this->redirect(Yii::app()->request->baseUrl . '/index.php/cls/myClasses');
									}
							}
						else
							{
								$this->redirect(Yii::app()->request->baseUrl . 'index.php');
							}
					}
				}
				// End of Login Functions
				
		$this->render('index', array('model'=>$model));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
	
	/*
	 * Displays fake setting Page
	 */
	public function actionSettings()
		{
			$this->render('settings');
		}
	/*
	 * Password Reset Form Page
	 */
	public function actionPwReset()
	{
		$this->render('pwReset');
	}

	/*
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				{
					if(Yii::app()->user->checkAccess('school_admin'))
						{
							$this->redirect('index');
						}
					elseif(Yii::app()->user->checkAccess('teacher'))
					{
						// TO DO: Reference the Current Time and Find the Current Period
						$userId = Yii::app()->user->id;
						// Turn Off Scope?
						$userSchoolId = User::model()->findByPk($userId)->school_id;
						$currentTime = gmdate('H:i:s');  // TO DO: Make sure it gets current GMT
						
						// New Class Finder
						$currentClass = Yii::app()->db->createCommand('SELECT id FROM `cls` WHERE period_id IN 
						(SELECT id FROM `period` WHERE (period_start_time <= UTC_TIME() AND period_end_time >= UTC_TIME()) AND school_id=' . $userSchoolId . ');')->queryColumn();
						//
						//print_r($currentClass);
						
						if(count($currentClass) == 1)
							{
								$currentClassId = $currentClass[0];
								//print "Current Period ID: " . $currentPeriodId . "<br />";	
								$this->redirect('../cls/' . $currentClassId);
							}
						else
							{
								$this->redirect('../cls');
							}
					}
					else
						{
							$this->redirect(Yii::app()->user->returnUrl);
						}
					}
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/*
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}