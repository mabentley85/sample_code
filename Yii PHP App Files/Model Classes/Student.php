<?php

/**
 * This is the model class for table "student".
 *
 * The followings are the available columns in table 'student':
 * @property integer $id
 * @property integer $school_id
 * @property integer $school_issued_id
 * @property string $student_first_name
 * @property string $student_last_name
 * @property string $student_year
 * @property string $create_time
 * @property string $update_time
 * @property integer $update_user
 * @property integer $isDeleted
 *
 * The followings are the available model relations:
 * @property Contact[] $contacts
 * @property School $school
 * @property Cls[] $cls
 */
class Student extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Student the static model class
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
		return 'student';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, student_first_name, student_last_name, create_time, update_user, isDeleted', 'required'),
			array('school_id, school_issued_id, update_user', 'numerical', 'integerOnly'=>true),
			array('student_first_name, student_last_name', 'length', 'max'=>128),
			array('student_year', 'length', 'max'=>8),
			array('update_time', 'safe'),
			array('id', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, school_id, school_issued_id, student_first_name, student_last_name, student_year, create_time, update_time, update_user, isDeleted', 'safe', 'on'=>'search'),
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
			'contacts' => array(self::MANY_MANY, 'Contact', 'student_contact(student_id, contact_id)'),
			'student_contact'=>array(self::HAS_MANY, 'StudentContact','student_id'),
			'school' => array(self::BELONGS_TO, 'School', 'school_id'),
			'cls' => array(self::MANY_MANY, 'Cls', 'student_class(student_id, class_id)'),
			'student_class'=>array(self::HAS_MANY, 'StudentClass','student_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Student ID',
			'school_id' => 'School',
			'school_issued_id' => 'School Issued ID',
			'student_first_name' => 'First Name',
			'student_last_name' => 'Last Name',
			'student_year' => 'Student Year',
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
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('school_issued_id',$this->school_issued_id);
		$criteria->compare('student_first_name',$this->student_first_name,true);
		$criteria->compare('student_last_name',$this->student_last_name,true);
		$criteria->compare('student_year',$this->student_year,true);
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
				// Return only Students in the District
				$userDistrictId = User::model()->findByPk($userId)->district_id;
				return array('condition'=>'school_id IN (SELECT DISTINCT id FROM `school` WHERE district_id="' . $userDistrictId . '")',);
			}
        elseif(User::model()->findByPk($userId)->role == 'school_admin' || User::model()->findByPk($userId)->role == 'teacher')
			{
				// Returns only students in the user's School
				$userSchoolId = User::model()->findByPk($userId)->school_id;
				return array('condition'=>'school_id="' . $userSchoolId . '"',);
			}
    }	
	
	
	public function getStudentYearList()
		{
			$yearList = array('Freshman'=>'Freshman','Sophmore'=>'Sophmore', 'Junior'=>'Junior', 'Senior'=>'Senior');
			return $yearList;
		}
	
	protected function beforeValidate()
	{
		if(!Yii::app()->user->checkAccess('district_admin'))
		{
			$this->school_id = User::model()->findByPk(Yii::app()->user->id)->school_id;
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