<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property integer $district_id
 * @property integer $school_id
 * @property string $username
 * @property string $password
 * @property string $new_password
 * @property string $new_password_repeat
 * @property string $salt
 * @property string $user_first_name
 * @property string $user_last_name
 * @property string $role
 * @property string $create_time
 * @property string $update_time
 * @property integer $update_user
 * @property integer $isDeleted
 *
 * The followings are the available model relations:
 * @property Cls[] $cls
 * @property Contact[] $contacts
 * @property District $district
 * @property School $school
 */
class User extends CActiveRecord
{
	// Constants for Drop Down List
	const TYPE_TEACHER='teacher';
	const TYPE_SCHOOL_ADMIN='school_admin';
	const TYPE_DISTRICT_ADMIN='district_admin';
	const TYPE_ADMIN='admin';
	
	public $new_password;
	public $new_password_repeat;
		
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password, salt, user_first_name, user_last_name, role, create_time, update_user, district_id, isDeleted, school_id', 'required'),
			array('update_user, district_id, school_id', 'numerical', 'integerOnly'=>true),
			array('username, password', 'length', 'max'=>256),
			array('salt, user_first_name, user_last_name', 'length', 'max'=>128),
			array('role', 'length', 'max'=>14),
			array('update_time, new_password, new_password_repeat', 'safe'),
			array('id, username', 'unique'),
			array('new_password', 'compare', 'on'=>'changePw'),
			array('new_password, new_password_repeat', 'required', 'on'=>'changePw'),
			array('username', 'email'),		
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, salt, user_first_name, user_last_name, role, create_time, update_time, update_user, district_id, isDeleted, school_id',
			 'safe', 'on'=>'search'),
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
			'cls' => array(self::HAS_MANY, 'Cls', 'user_id'),
			'contacts' => array(self::MANY_MANY, 'Contact', 'user_contact(user_id, contact_id)'),
			'district' => array(self::BELONGS_TO, 'District', 'district_id'),
			'school' => array(self::BELONGS_TO, 'School', 'school_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'User ID',
			'username' => 'Email Address',
			'password' => 'Password',
			'new_password'=>'New Password',
			'new_password_repeat'=>'Confirm New Password',
			'salt' => 'Salt',
			'user_first_name' => 'First Name',
			'user_last_name' => 'Last Name',
			'role' => 'Role',
			'create_time' => 'Created On',
			'update_time' => 'Last Updated On',
			'update_user' => 'Last Updated By',
			'district_id' => 'District',
			'isDeleted' => 'isDeleted',
			'school_id' => 'School',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		//$criteria->compare('new_password',$this->password, true);
		//$criteria->compare('new_password_repeat', $this->new_password_repeat, true);
		$criteria->compare('salt',$this->salt, true);
		$criteria->compare('user_first_name',$this->user_first_name,true);
		$criteria->compare('user_last_name',$this->user_last_name,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('update_user',$this->update_user);
		$criteria->compare('district_id',$this->district_id, true);
		$criteria->compare('isDeleted', $this->isDeleted, true);
		$criteria->compare('school_id', $this->school_id, true);
		/////
		$criteria->condition = 'district_id=:districtID';
		$criteria->params = array(':districtID' => $this->district_id); 
		///
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	
	/*
	 * Add required fields depending on User Role
	 */
	
	protected function beforeValidate()
	{
			
		if($this->isNewRecord)
		{
			$this->new_password = $this->password;
			$this->new_password_repeat = $this->password;
		}
		
		if(Yii::app()->user->checkAccess('distirct_admin'))
			{
				$this->district_id = User::model()->findByPk(Yii::app()->user->id)->district_id;
			}
		elseif(Yii::app()->user->checkAccess('school_admin'))
			{
				$this->district_id = User::model()->findByPk(Yii::app()->user->id)->district_id;
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
	
	
	/*
	 * Perform One-way encryption on the password before we store it in the database
	 */
	protected function afterValidate()
	{
		parent::afterValidate();
		$this->password = $this->encrypt($this->password);
	}
	
	public function encrypt($value)
	{
		return md5($value);	
	}
	
	public function getUserRoleOptions()
	{
		$userRoles = array();
		if (Yii::app()->user->checkAccess("school_admin"))
		{
			$userRoles['teacher'] = 'Teacher';
		}
		if (Yii::app()->user->checkAccess("district_admin"))
		{
			$userRoles['school_admin'] ='School Admin';
		}
		if (Yii::app()->user->checkAccess("admin"))
		{
			$userRoles['district_admin'] = 'District Admin';
		}
		return $userRoles;
		
	}
	
	public function getTeacherList($role, $districtId, $schoolId)
		{
			$teacherList = array();	
			if(Yii::app()->user->checkAccess('admin'))
				{
					$teacherList = CHtml::listData($this->findAll('role=:role', array(':role'=>'teacher')), 'id', 'username');
				}
			elseif(Yii::app()->user->checkAccess('district_admin'))
				{
					$teacherList = CHtml::listData($this->findAll('role=:role AND district_id=:districtId', array(':role'=>'teacher',':districtId'=>$districtId,)), 'id', 'username');
				}
			elseif(Yii::app()->user->checkAccess('school_admin'))
				{
					$teacherList = CHtml::listData($this->findAll('role=:role AND school_id=:schoolId', array(':role'=>'teacher',':schoolId'=>$schoolId,)), 'id', 'username');
				}
			return $teacherList;
		}
		
	/*
	 * Restricts Who Can See What
	 *  TO DO: Get Users Logged In Before Scope is Set
	 */
	
	public function scopes()
    {
        $userId = Yii::app()->user->id;
		
		if(User::model()->findByPk($userId)->role == 'admin')
			{
				// No Restrictions
				return array('userScope'=>array());
			}
		elseif(User::model()->findByPk($userId)->role == 'district_admin')
			{
				// Return only Users in the current user's District
				$userDistrictId = User::model()->findByPk($userId)->district_id;
				return array('userScope'=>array('condition'=>'school_id IN (SELECT DISTINCT id FROM `school` WHERE district_id="' . $userDistrictId . '") AND 
					role="teacher" OR role="school_admin" OR id="' . $userId . '"'));
			}
        elseif(User::model()->findByPk($userId)->role == 'school_admin' || User::model()->findByPk($userId)->role == 'teacher')
			{
				// Returns only Users in the current user's School
				$userSchoolId = User::model()->findByPk($userId)->school_id;
				return array('userScope'=>array('condition'=>'school_id="' . $userSchoolId . '" AND role="teacher" OR id="' . $userId . '"'));
			}
		elseif(User::model()->findByPk($userId)->role == 'teacher')
			{
				return array('userScope'=>array('condition'=>'id="' . $userId . '"',));
			}
		}
}