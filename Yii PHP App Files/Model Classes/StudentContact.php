<?php
class StudentContact extends CActiveRecord
{
    /**
 * This is the model class for table "student_contact".
 *
 * The followings are the available columns in table 'student_contact':
 * @property integer $student_id
 * @property integer $contact_id
 * @property string $update_time
 * @property integer $update_user
 *
 * The followings are the available model relations:
 * @property Contact $contact
 * @property Student $student
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
		return 'student_contact';
	}
    
    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, contact_id, update_time, update_user', 'required'),
			array('student_id, contact_id, update_user', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('student_id, contact_id, update_time, update_user', 'safe', 'on'=>'search'),
		);
	}
    
    public function relations()
    {
        return array(
            'contact'=>array(self::BELONGS_TO, 'Contact', 'contact_id'), // Might be ID Column instead of "contact_id"
            'student'=>array(self::BELONGS_TO, 'Student', 'student_id'), // Might be ID Column instead of "student_id"
        );
    }
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'student_id' => 'Student ID',
			'contact_id' => 'contact ID',
			'update_time' => 'Last Updated On',
			'update_user' => 'Last Updated By',
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

		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('contact_id',$this->contact_id);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('update_user',$this->update_user);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
}