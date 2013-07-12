<?php

/**
 * This is the model class for table "district".
 *
 * The followings are the available columns in table 'district':
 * @property integer $id
 * @property string $district_name
 * @property string $create_time
 * @property string $update_time
 * @property integer $update_user
 * @property integer $isDeleted
 * @property string $timeZone
 *
 * The followings are the available model relations:
 * @property School[] $schools
 */
class District extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return District the static model class
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
		return 'district';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('district_name, timeZone, create_time, update_user, isDeleted', 'required'),
			array('update_user', 'numerical', 'integerOnly'=>true),
			array('district_name', 'length', 'max'=>128),
			array('update_time', 'safe'),
			array('id', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, district_name, timeZone, create_time, update_time, update_user, isDeleted', 'safe', 'on'=>'search'),
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
			'schools' => array(self::HAS_MANY, 'School', 'district_id'),
			'users' => array(self::HAS_MANY, 'User', 'district_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'District ID',
			'district_name' => 'District Name',
			'timeZone'=>'Time Zone',
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
		$criteria->compare('district_name',$this->district_name,true);
		$criteria->compare('timeZone',$this->timeZone);
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
		elseif((User::model()->findByPk($userId)->role == 'district_admin') || 
				(User::model()->findByPk($userId)->role == 'school_admin') || 
				(User::model()->findByPk($userId)->role == 'teacher'))
			{
				// Return only the user's District
				$userDistrictId = User::model()->findByPk($userId)->district_id;
				// TO DO: FIX!!!
				return array('condition'=>'id="' . $userDistrictId . '"');
			}
    }
	
	
	public function getDistrictList()
	{
		$districtArray = CHtml::listData($this->findAll(), 'id', 'district_name');
		return $districtArray;	
	}
	
	public function getTimeZones()
	{
		$timeZones = array();
		$timeZones['ET'] = 'Eastern Time';
		$timeZones['CT'] = 'Central Time';
		$timeZones['MT'] = 'Moutain Time';
		$timeZones['PT'] = 'Pacific Time';
		return $timeZones;
		
	}
	
	protected function beforeValidate()
	{
			
		if(!$this->create_time)
		{
			$this->create_time = date("Y-m-d\TH:i:s\Z", time());
		}
		$this->update_time = date("Y-m-d\TH:i:s\Z", time());
		$this->update_user = Yii::app()->user->id;
				
		return parent::beforeValidate();
	}
	
}