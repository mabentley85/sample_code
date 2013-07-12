<?php

/**
 * This is the model class for table "school".
 *
 * The followings are the available columns in table 'school':
 * @property integer $id
 * @property integer $district_id
 * @property string $school_name
 * @property string $daily_student_funding
 * @property string $initial_truancy_rate
 * @property string $current_truancy_rate
 * @property string $create_time
 * @property string $update_time
 * @property integer $update_user
 * @property integer $isDeleted
 *
 * The followings are the available model relations:
 * @property Cls[] $cls
 * @property Period[] $periods
 * @property District $district
 * @property Student[] $students
 */
class School extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return School the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'school';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('district_id, school_name, create_time, update_user, isDeleted', 'required'),
			array('district_id, update_user', 'numerical', 'integerOnly'=>true),
			array('school_name', 'length', 'max'=>256),
			array('daily_student_funding', 'length', 'max'=>6),
			array('initial_truancy_rate, current_truancy_rate', 'length', 'max'=>2),
			array('update_time', 'safe'),
			array('id', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, district_id, school_name, daily_student_funding, initial_truancy_rate, current_truancy_rate, create_time, update_time, update_user, isDeleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'cls' => array(self::HAS_MANY, 'Cls', 'school_id'),
			'periods' => array(self::HAS_MANY, 'Period', 'school_id'),
			'district' => array(self::BELONGS_TO, 'District', 'district_id'),
			'students' => array(self::HAS_MANY, 'Student', 'school_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'School ID',
			'district_id' => 'District ID',
			'school_name' => 'School Name',
			'daily_student_funding' => 'Daily Student Funding ($)',
			'initial_truancy_rate' => 'Initial Truancy Rate (%)',
			'current_truancy_rate' => 'Current Truancy Rate (%)',
			'create_time' => 'Created On',
			'update_time' => 'Last Updated On',
			'update_user' => 'Last Updated By',
			'isDeleted' => 'isDeleted',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('district_id',$this->district_id);
		$criteria->compare('school_name',$this->school_name,true);
		$criteria->compare('daily_student_funding',$this->daily_student_funding,true);
		$criteria->compare('initial_truancy_rate',$this->initial_truancy_rate,true);
		$criteria->compare('current_truancy_rate',$this->current_truancy_rate,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('update_user',$this->update_user);
		$criteria->compare('isDeleted', $this->isDeleted);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	/*
	 * Restricts Who Can See What
	 */
	public function defaultScope()
    {
        $userId = Yii::app()->user->id;
		
        if(User::model()->findByPk($userId)->role == 'admin')
			{
				// No Restrictions
				return array();
			}
		elseif(User::model()->findByPk($userId)->role == 'district_admin')
			{
				// Return only Schools in the District
				$userDistrictId = User::model()->findByPk($userId)->district_id;
				return array('condition'=>'district_id="' . $userDistrictId . '"',);
			}
        elseif(User::model()->findByPk($userId)->role == 'school_admin' || User::model()->findByPk($userId)->role == 'teacher')
			{
				// Returns the user's School
				$userSchoolId = User::model()->findByPk($userId)->school_id;
				return array('condition'=>'id="' . $userSchoolId . '"',);
			}
    }	
	
	public function getSchoolList($role, $districtId, $schoolId)
	{
		$schoolArray = array();
			
		if($role == "admin")
			{
				$schoolArray = CHtml::listData($this->findAll(), 'id', 'school_name');
			}
		elseif($role == "district_admin")
			{
				$schoolArray = CHtml::listData($this->findAll('district_id=:districtId' ,array(':districtId'=>$districtId)), 'id', 'school_name');				
			}
		return $schoolArray;	
	}
	
	protected function beforeValidate()
	{
		if(!Yii::app()->user->checkAccess('admin'))
		{
			$this->district_id = User::model()->findByPk(Yii::app()->user->id)->district_id;
		}
		
		if(!$this->create_time)
		{
			$this->create_time = date("Y-m-d\TH:i:s\Z", time());
		}
		$this->update_time = date("Y-m-d\TH:i:s\Z", time());
		$this->update_user = Yii::app()->user->id;
		
		return parent::beforeValidate();
	}
	
}