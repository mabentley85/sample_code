<?php

/**
 * This is the model class for table "contact".
 *
 * The followings are the available columns in table 'contact':
 * @property integer $id
 * @property integer $user_id
 * @property integer $student_id
 * @property integer $school_id
 * @property string $method
 * @property string $data
 * @property string $contact_name
 * @property string $create_time
 * @property string $update_time
 * @property integer $update_user
 * @property integer $isDeleted
 * @property integer $stopTruantAlert
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Student $student
 * @property Student[] $students
 * @property User[] $users
 */
class Contact extends CActiveRecord
{
	const METHOD_TEXT="text";
	const METHOD_EMAIL="email";
	const METHOD_VOICE="voice";
		
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Contact the static model class
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
		return 'contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('method, contact_name, data, create_time, update_user, isDeleted, stopTruantAlert, school_id', 'required'),
			array('user_id, student_id, update_user, school_id', 'numerical', 'integerOnly'=>true),
			array('method', 'length', 'max'=>5),
			array('data, contact_name', 'length', 'max'=>128),
			array('update_time', 'safe'),
			array('id', 'unique'),
			array('data', 'email', 'on'=>array('emailBool')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, student_id, school_id, method, data, contact_name, create_time, update_time, update_user, isDeleted, stopTruantAlert', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'student' => array(self::BELONGS_TO, 'Student', 'student_id'),
			'students' => array(self::MANY_MANY, 'Student', 'student_contact(contact_id, student_id)'),
			'student_contact'=>array(self::HAS_MANY, 'StudentContact','contact_id'),
			'users' => array(self::MANY_MANY, 'User', 'user_contact(contact_id, user_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'student_id' => 'Student',
			'school_id'=>'School ID',
			'method' => 'Method',
			'data' => 'Phone Number or Email Address',
			'contact_name' => 'Contact Name',
			'create_time' => 'Created On',
			'update_time' => 'Last Updated On',
			'update_user' => 'Last Updated By',
			'isDeleted' => 'isDeleted',
			'stopTruantAlert'=>'Truancy Alerts Stopped',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('school_id', $this->school_id);
		$criteria->compare('method',$this->method,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('update_user',$this->update_user);
		$criteria->compare('isDeleted', $this->isDeleted);
		$criteria->compare('stopTruantAlert', $this->stopTruantAlert);
		
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
		
		if(Yii::app()->user->getIsGuest())
			{
				// TO DO: Allow for the searching of Phone number by the Twilio Reply Feature
				return array();				
			}
		
		elseif(User::model()->findByPk($userId)->role == 'admin')
			{
				// No Restrictions
				return array();
			}
		elseif(User::model()->findByPk($userId)->role == 'district_admin')
			{
				// Return only Contacts in the District
				$userDistrictId = User::model()->findByPk($userId)->district_id;
				return array('condition'=>'school_id IN (SELECT DISTINCT id FROM school WHERE district_id="' . $userDistrictId . '")');
			}
        elseif(User::model()->findByPk($userId)->role == 'teacher' || User::model()->findByPk($userId)->role == 'school_admin')
			{
				// Return Contacts in the School
				$userSchoolId = User::model()->findByPk($userId)->school_id;
				return array('condition'=>'school_id="' . $userSchoolId . '"',);
			}
		
    }
	
	public function getContactMethods()
	{
		$contactMethods = array(
			self::METHOD_TEXT=>'Text Message',
			self::METHOD_EMAIL=>'eMail',
			self::METHOD_VOICE=>'Voice Call'
		);
		return $contactMethods;
	}
	
	protected function beforeValidate()
		{
			if(!Yii::app()->user->checkAccess('district_admin'))
				{
					//School ID
					$this->school_id = User::model()->findByPk(Yii::app()->user->id)->school_id;
				}
			
			if (!$this->create_time)
				{
					$this->create_time = date("Y-m-d\TH:i:s\Z", time());
				}
			$this->update_time = date("Y-m-d\TH:i:s\Z", time());
			$this->update_user = Yii::app()->user->id;
			
			return parent::beforeValidate();
		}
	
}