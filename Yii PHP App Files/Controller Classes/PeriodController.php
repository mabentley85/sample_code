<?php

class PeriodController extends Controller
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
				'actions'=>array('index', 'dropDownPeriods', ),
				'users'=>array('@'),
			),
			array('allow',  // allow Authenticated users to perform 'view' action
				'actions'=>array('view'),
				'expression'=>'Yii::app()->user->checkAccess("viewPeriod")',
			),
			array('allow', // allow authenticated user to perform 'create' action
				'actions'=>array('create'),
				'expression'=>'Yii::app()->user->checkAccess("createPeriod")',
			),
			array('allow', // allow authenticated user to perform 'update' action
				'actions'=>array('update'),
				'expression'=>'Yii::app()->user->checkAccess("updatePeriod")',
			),
			array('allow', // allow authenticated user to perform 'admin' action
				'actions'=>array('admin'),
				'expression'=>'Yii::app()->user->checkAccess("adminPeriod")',
			),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('delete'),
				'expression'=>'Yii::app()->user->checkAccess("deletePeriod")',
				),
			array('allow', // allow authenticated user to perform 'delete' action
				'actions'=>array('softDelete'),
				'expression'=>'Yii::app()->user->checkAccess("deletePeriod")',
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
		$model=new Period;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Period']))
		{
			$model->attributes=$_POST['Period'];
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

		if(isset($_POST['Period']))
		{
			$model->attributes=$_POST['Period'];
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
		$softDelete = Period::model()->findByPk($id);
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
		if (Yii::app()->user->checkAccess('admin'))
		{
			$dataProvider=new CActiveDataProvider('Period', array('criteria'=>array('condition'=>'isDeleted=0'),));
		}
		elseif (Yii::app()->user->checkAccess('district_admin'))
		{
			// Gets User's District
			$userDistrictId = User::model()->findByPk(Yii::app()->user->id)->district_id;
			$criteria = new CDbCriteria;
			// Use "with" to add a 2nd table to reference to
			$criteria->with = array('school'); 
			// Use "Condition" for the 'WHERE' statement in SQL
			$criteria->condition = 'district_id=:districtId AND t.isDeleted=0';
			$criteria->params = array(':districtId'=>$userDistrictId);
						
			$dataProvider = new CActiveDataProvider('Period', array('criteria'=> $criteria), true);
		}
		elseif (Yii::app()->user->checkAccess('school_admin'))
		{
			$dataProvider=new CActiveDataProvider('Period', 
							array('criteria'=>array(
											'condition'=>'isDeleted=0 
											&& school_id=' . User::model()->findByPk(Yii::app()->user->id)->school_id),));
		}
			
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Period('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Period']))
			$model->attributes=$_GET['Period'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/*
	 * Returns a Drop Down list of Periods Based on What school is choosen from another Drop Down List
	 */
	 	
	public function actionDropDownPeriods()
	{
		// Gets all the Periods with the selected school ID
		$data = Period::model()->findAll('school_id=:parent_id', array(':parent_id'=>(int) $_POST['school_id'])); 
 		// Creates a list with just the "id" and "period_name" values from the period table 
		$data = CHtml::listData($data,'id','period_name');
 		
 		$return_periods = '';
    	foreach($data as $value=>$name)
    	{
        	// Creates an Option/Value Pair for each Id and corresponding Period name, the ".=" symbol means add on to the end
			$return_periods .= CHtml::tag('option', array('value'=>$value),CHtml::encode($name),true);  
    	}
		echo $return_periods;
	}
	
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Period::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='period-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
}