<?php

/**
 * This is the model class for table "cls".
 *
 * The followings are the available columns in table 'cls':
 * @property integer $id
 * @property integer $period_id
 * @property integer $school_id
 * @property integer $user_id
 * @property string $class_name
 * @property string $create_time
 * @property string $update_time
 * @property integer $update_user
 * @property integer $isDeleted
 *
 * The followings are the available model relations:
 * @property Period $period
 * @property School $school
 * @property User $user
 * @property Student[] $students
 */
class Cls extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Cls the static model class
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
		return 'cls';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('period_id, school_id, user_id, class_name, create_time, update_time, update_user, isDeleted', 'required'),
			array('period_id, school_id, user_id, update_user', 'numerical', 'integerOnly'=>true),
			array('class_name', 'length', 'max'=>128),
			array('id', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, period_id, school_id, user_id, class_name, create_time, update_time, update_user, isDeleted', 'safe', 'on'=>'search'),
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
			'period' => array(self::BELONGS_TO, 'Period', 'period_id'),
			'school' => array(self::BELONGS_TO, 'School', 'school_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'students' => array(self::MANY_MANY, 'Student', 'student_class(class_id, student_id)'),
			'student_class'=>array(self::HAS_MANY, 'StudentClass','class_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Class ID',
			'period_id' => 'Period',
			'school_id' => 'School',
			'user_id' => 'Teacher',
			'class_name' => 'Class Name',
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
		$criteria->compare('period_id',$this->period_id);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('class_name',$this->class_name,true);
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
				// Return only Classes in the District
				$userDistrictId = User::model()->findByPk($userId)->district_id;
				return array('condition'=>'school_id IN (SELECT DISTINCT id FROM `school` WHERE district_id="' . $userDistrictId . '")');
			}
        elseif(User::model()->findByPk($userId)->role == 'school_admin')
			{
				// Return Classes in the School
				$userSchoolId = User::model()->findByPk($userId)->school_id;
				return array('condition'=>'school_id="' . $userSchoolId . '"',);
			}
		elseif(User::model()->findByPk($userId)->role == 'teacher')
			{
				return array('condition'=>'user_id="' . $userId . '"',);
			}
    }
	
	protected function beforeValidate()
		{
			if(!Yii::app()->user->checkAccess('district_admin'))
				{
					// School ID
					$this->school_id = User::model()->findByPk(Yii::app()->user->id)->school_id;
				}
			if(!Yii::app()->user->checkAccess('school_admin'))
				{
					// User ID
					$this->user_id = Yii::app()->user->id;
				}
			
			$this->update_user = Yii::app()->user->id;
			
			return parent::beforeValidate();
		}
	
	
}