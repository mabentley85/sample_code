<?php

class ReplyController extends Controller
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
			
			/*
			 * $log = new Log;
			 * $log->action_description = "Phone Number: " . $phone . "\nMessage: " . $message . "\nThe End.";
			 * $log->save();
			 * print "New Log Entry!<br />";
			 */			
	}
	
}