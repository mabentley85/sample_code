<?php

class TwilioController extends Controller
{
	
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
			array('allow',  // allow Authenticated users to perform action
				'actions'=>array('sendMessage'),
				'expression'=>'Yii::app()->user->checkAccess("sendMessage")',
			),
			array('allow',  // allow Authenticated users to perform action
				'actions'=>array('twilioTxt'),
				'expression'=>'Yii::app()->user->checkAccess("sendMessage")',
			),
			array('allow',  // allow Authenticated users to perform action
				'actions'=>array('checkBox'),
				'expression'=>'Yii::app()->user->checkAccess("sendMessage")',
			),
			array('allow',  // allow Authenticated users to perform action
				'actions'=>array('twilioTextReply'),
				'expression'=>'Yii::app()->user->isGuest',
			),
		);
	}	
	
	/*
	 * Based on which students' "Truant Check Boxes" are selected, the action looks up all the contact information
	 * associated with those students.  Then sends messages via their method of choice.
	 */
	
	public function actionCheckBox()
		{
			$schoolName = School::model()->findByPk($_POST['schoolId'])->school_name;
			$periodName = Period::model()->findByPk($_POST['periodId'])->period_name;
			$classId = $_POST['classId'];
			if(isset($_POST['truantCheckBox']))
				{
					
					$results = $_POST['truantCheckBox'];
					$flashError = ""; // Initializes Flash Error
					$flashSuccess = ""; // Initializes Flash Success
					
					foreach($results as $id)
						{
							$studentName =  CHtml::encode(Student::model()->findByPk($id)->student_first_name) . " " . 
							CHtml::encode(Student::model()->findByPk($id)->student_last_name);
							// Connect each Student ID with related Contact ID's
							$rawData = new CActiveDataProvider('Contact', array(
											'criteria'=>array(
												'with'=>array('student_contact'),
												'condition'=>'student_contact.student_id=:studentId',
												'params'=>array(':studentId'=>$id),
												'together'=>true,
												),
											)
										);
							$message = $studentName . " is currently marked absent for " . $periodName . " at " . $schoolName . ".";
							
							////
							foreach($rawData->data as $v)
							{
								 // TO DO: Add Verification that the Student ID and Contact ID match the Cell Phone Number
								 if($v->method == "text")
								 	{
								 		if($v->stopTruantAlert != 0)
											{
												$flashError =  '<b>' . $studentName . '</b>: ' . $v->contact_name . ' has requested to no longer receive truancy notices.';
											}	
										elseif($v->stopTruantAlert == 0)
											{
												if(self::actionTwilioTxt($v->data, $message))
													{
														$flashSuccess .= "<b>" . $studentName . ":</b> " . $v->contact_name . "  via " . $v->method . " at " . $v->data . ".<br />";
													}
											}
									}
								 elseif($v->method == "voice")
								 	{
								 		if($v->stopTruantAlert != 0)
											{
												$flashError =  '<b>' . $studentName . '</b>: ' . $v->contact_name . ' has requested to no longer receive truancy notices.';
											}	
										elseif($v->stopTruantAlert == 0)
											{
								 				//$flashError = "We haven't set up Voice Notifications Yet.  Yell at Matthew.";
								 				if(self::actionTwilioVoice($v->data, $message))
													{
														$flashSuccess .= "<b>" . $studentName . ":</b> " . $v->contact_name . "  via " . $v->method . " at " . $v->data . ".<br />";
													}
												}											 
								 	}
								 elseif($v->method == "email")
								 	{
								 		if($v->stopTruantAlert != 0)
											{
												$flashError =  '<b>' . $studentName . '</b>: ' . $v->contact_name . ' has requested to no longer receive truancy notices.';
											}	
										elseif($v->stopTruantAlert == 0)
											{
												//$flashError = "We haven't set up Email Notifications Yet.  Yell at Matthew";
								 				if(self::actionEmail($v->data, $v->contact_name, $schoolName, $message))
													{
														$flashSuccess .= "<b>" . $studentName . ":</b> " . $v->contact_name . "  via " . $v->method . " at " . $v->data . ".<br />";
													}
											}
								 	}
							}
							$flashSuccess .= "<br />";
							////
							
						}
					// Messages Sent Flash and Redirect
					if($flashError != "")
						{
							Yii::app()->user->setFlash('error', $flashError);
						}
					if(strlen($flashSuccess) > 6)
						{
							Yii::app()->user->setFlash('success', $flashSuccess);
						}	
					$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('cls/' . $classId));
				}
			else
				{
					Yii::app()->user->setFlash('error', 'No students were marked truant.  No Messages Sent.');	
					$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('cls/' . $classId));
					
					
				}			
		}
	
	/*
	 * Sends a Text Via Twilio API
	 */
	public function actionTwilioTxt($cellNumber, $message)
	{
		include_once("Twilio.php");
		
		// Twilio REST API version
		$ApiVersion = "2010-04-01";
		
		// Set our AccountSid and AuthToken
		$AccountSid = "XXXXXXXXXXXXXXXXXXXXXXXXXXXX"; // My Account SID
		$AuthToken = "XXXXXXXXXXXXXXXXXXXXXXXXXXXX";  // My Authorization Token
		
		// Instantiate a new Twilio Rest Client
		$client = new TwilioRestClient($AccountSid, $AuthToken);
		
		// Send a new outgoinging SMS by POST'ing to the SMS resource */
		// YYY-YYY-YYYY must be a Twilio validated phone number
		$response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages",
			"POST", array(
				"To" => $cellNumber,
				"From" => "XXX-XXX-XXXX",
				"Body" => $message,
				));
		if($response->IsError)
			{
				return 0; //echo "Error: {$response->ErrorMessage}";
			}
		else
			{
				return 1; //echo "Sent message to $studentName";
			}
	}

	/*
	 *  Sends an Email via YiiMail/SwiftMail Extension using info@truanttoday.com email account
	 */
	
	public function actionEmail($email, $contactName, $schoolName, $body)
		{
			$message = new YiiMailMessage();
            $message->setTo(array($email=>$contactName));
            $message->setFrom(array('info@truanttoday.com'=>'Truant Alert'));
            $message->setSubject('Truant Alert from ' . $schoolName);
            $message->setBody($body);
 			
            if(Yii::app()->mail->send($message))
				{
					return 1;
				}
			else
				{
					return 0;
				}
									
		}
	
	/*
	 * Send an automated Voice call Via Twilio API
	 */
	
	public function actionTwilioVoice($phoneNumber, $message)
		{
			include_once("Twilio.php");
			
			/* Twilio REST API version */
			$ApiVersion = "2010-04-01";
			
			/* Set our AccountSid and AuthToken */
			$AccountSid = "XXXXXXXXXXXXXXXXXXXXXXXXXXXX";
			$AuthToken = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
			
			/* Directory location for callback.php file (for use in REST URL)*/
			// NOTE: Does Not work when Locally Hosted
			$url = 'http://alpha.truanttoday.com/temp/TruantCall.php';
			
			$message = urlencode($message);
					
			/* Instantiate a new Twilio Rest Client */
			$client = new TwilioRestClient($AccountSid, $AuthToken);
			
			/* make Twilio REST request to initiate outgoing call */
			$response = $client->request("/$ApiVersion/Accounts/$AccountSid/Calls",
				"POST", array(
					"From" => 'XXX-XXX-XXXX',
					"To" => $phoneNumber,
					"Url" => $url . "?message=" . $message));
			
			if($response->IsError)
				{
					return 0; // Error 
				}
			else
				{
					return 1; // Message Sent
				}

		} // End of Voice Function
		
	/*
	 * When our Twilio number receives an incoming Text Message it is directed to this function
	 * Based on the contentd of the Body of the Text message, furture text messages will not be sent
	 * to that number is "stop" is in the text body, messages will resume if "start" is in the body.
	 */
	public function actionTwilioTextReply()
	{
			
		$phone = $_REQUEST['From'];	// Incoming Phone Number
		$message = $_REQUEST['Body'];	// Incoming Text Message
		$stopBool = stripos($message , 'stop');
		if(!$stopBool === false)
			{
				// Trim the Phone Number
				$phone = substr($phone,2,10);
				
				// Find a contact record with the matching Phone Number
				$stopTruantAlerts = Contact::model()->resetScope()->find('data=:phone', array(':phone'=>$phone));
				
				// Stop Messages
				if(!$stopTruantAlerts->stopTruantAlert == 1)
					{
						$stopTruantAlerts->stopTruantAlert = 1;
						$stopTruantAlerts->save();
						//print "Truant Alerts Stopped<br />";
					}
			}
			
			$startInt = stripos($message , 'start');
			if(!$startInt === false)
				{
					// Trim the Phone Number
					$phone = substr($phone,2,10);
					
					// Find a contact record with the matching Phone Number
					$startTruantAlerts = Contact::model()->resetScope()->find('data=:phone', array(':phone'=>$phone));
					
					if(!$startTruantAlerts->stopTruantAlert == 0)
						{
							$startTruantAlerts->stopTruantAlert = 0;
							$startTruantAlerts->save();
						}
				}
			
			// Sick Reply Message
			$sickInt = stripos($message , 'sick');
			if(!$sickInt === false)
				{
					$phone = substr($phone,2,10);
					$reply = 'Thank you for replying.  Your reply has been forwarded as an email to the dean of attendance informing them that your child is sick today.';
					//print "Sick Phone Number to Text: " . $phone . "<br />";
					//print "Message: " . $reply . "<br />";
					self::actionTwilioTxt($phone, $reply);
				}
								
			}	
}