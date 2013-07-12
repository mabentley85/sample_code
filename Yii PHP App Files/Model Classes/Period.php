<?php

/**
 * This is the model class for table "period".
 *
 * The followings are the available columns in table 'period':
 * @property integer $id
 * @property integer $school_id
 * @property string $period_name
 * @property string $period_start_time
 * @property string $period_end_time
 * @property integer $monday_bool
 * @property integer $tuesday_bool
 * @property integer $wednesday_bool
 * @property integer $thursday_bool
 * @property integer $friday_bool
 * @property string $create_time
 * @property string $update_time
 * @property integer $update_user
 * @property integer $isDeleted
 *
 * The followings are the available model relations:
 * @property Cls[] $cls
 * @property School $school
 */
class Period extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Period the static model class
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
		return 'period';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, period_name, period_start_time, period_end_time, create_time, update_user, isDeleted', 'required'),
			array('school_id, monday_bool, tuesday_bool, wednesday_bool, thursday_bool, friday_bool, update_user', 'numerical', 'integerOnly'=>true),
			array('period_name', 'length', 'max'=>128),
			array('update_time', 'safe'),
			array('id', 'unique'),			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, school_id, period_name, period_start_time, period_end_time, monday_bool, tuesday_bool, wednesday_bool, thursday_bool, friday_bool, create_time, update_time, update_user, isDeleted', 'safe', 'on'=>'search'),
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
			'cls' => array(self::HAS_MANY, 'Cls', 'period_id'),
			'school' => array(self::BELONGS_TO, 'School', 'school_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Period ID',
			'school_id' => 'School',
			'period_name' => 'Period Name',
			'period_start_time' => 'Start Time',
			'period_end_time' => 'End Time',
			'monday_bool' => 'Monday',
			'tuesday_bool' => 'Tuesday',
			'wednesday_bool' => 'Wednesday',
			'thursday_bool' => 'Thursday',
			'friday_bool' => 'Friday',
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
		$criteria->compare('period_name',$this->period_name,true);
		$criteria->compare('period_start_time',$this->period_start_time,true);
		$criteria->compare('period_end_time',$this->period_end_time,true);
		$criteria->compare('monday_bool',$this->monday_bool);
		$criteria->compare('tuesday_bool',$this->tuesday_bool);
		$criteria->compare('wednesday_bool',$this->wednesday_bool);
		$criteria->compare('thursday_bool',$this->thursday_bool);
		$criteria->compare('friday_bool',$this->friday_bool);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('update_user',$this->update_user);
		$criteria->compare('isDeleted', $this->isDeleted);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/*
	 *  Before Saving to the DB alter the Time format from AM/PM to 24HR
	 */
	protected function beforeSave()
		{
			
			if(parent::beforeSave())
                {
					/*
					// TO DO: Convert Time from Local to GMT
					// Get timeZone from District Table
					$schoolId = $this->school_id;
					$districtId = School::model()->findByPk($schoolId)->district_id;
					$timeZone = District::model()->findByPk($districtId)->timeZone;
					// Get +/- Hr Correction for timeZone
					if($timeZone == "ET")
						{
							$timeDif = 4;
						}
					elseif($timeZone == "CT")
						{
							$timeDif = 5;
						}
					elseif($timeZone == "MT")
						{
							$timeDif = 6;
						}
					elseif($timeZone == "PT")
						{
							$timeDif = 7;
						}
					// Apply Time Conversion
					$newStartHour = date('H', strtotime($this->period_start_time)) + $timeDif;
					*/
					$newStartTime = mktime(date('H', strtotime($this->period_start_time)), date('i', strtotime($this->period_start_time)), date('s', strtotime($this->period_start_time)));
					$this->period_start_time = date('H:i:s', $newStartTime);					
					
					// Set the GMT before save
					//$newEndHour = date('H', strtotime($this->period_end_time)) + $timeDif;
					$newEndTime = mktime(date('H', strtotime($this->period_end_time)), date('i', strtotime($this->period_end_time)), date('s', strtotime($this->period_end_time)));
					$this->period_end_time = date('H:i:s', $newEndTime);
				}
			
			return true;
			

		}
		
	protected function afterFind()
		{
			/*
			// Get timeZone from District Table
			$schoolId = $this->school_id;
			$districtId = School::model()->findByPk($schoolId)->district_id;
			$timeZone = District::model()->findByPk($districtId)->timeZone;
			
			// Get +/- Hr Correction for timeZone
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
			// Apply Time Conversion
			date_default_timezone_set($timeDif);
			*/
			
			//$newStartHour = date('H', strtotime($this->period_start_time)) - $timeDif;
			$newStartTime = mktime(date('H', strtotime($this->period_start_time)), date('i', strtotime($this->period_start_time)), date('s', strtotime($this->period_start_time)));
			$this->period_start_time = date('g:i A', $newStartTime);
			
			//$newEndHour = date('H', strtotime($this->period_end_time)) - $timeDif;
			$newEndTime = mktime(date('H', strtotime($this->period_end_time)), date('i', strtotime($this->period_end_time)), date('s', strtotime($this->period_end_time)));
			$this->period_end_time = date('g:i A', $newEndTime);
			
			
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
				// Return only Period in the District
				$userDistrictId = User::model()->findByPk($userId)->district_id;
				return array('condition'=>'school_id IN (SELECT DISTINCT id FROM `school` WHERE district_id="' . $userDistrictId . '")');
			}
        elseif(User::model()->findByPk($userId)->role == 'school_admin' || User::model()->findByPk($userId)->role == 'teacher')
			{
				// Returns Periods in the School
				$userSchoolId = User::model()->findByPk($userId)->school_id;
				return array('condition'=>'school_id="' . $userSchoolId . '"',);
			}
    }


	public function getPeriodList($role, $districtId, $schoolId)
		{
			$periodArray = array();
			if($role == "teacher" || $role == "school_admin")
				{
					$periodArray = CHtml::listData($this->findAll('school_id=:schoolId', array(':schoolId'=>$schoolId)), 'id', 'period_name');
				}
			
			return $periodArray;
		}
		
	protected function beforeValidate()
		{
			if(!Yii::app()->user->checkAccess('district_admin'))
			{
				// School ID
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