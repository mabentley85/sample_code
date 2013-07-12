//
//  AboutViewController.m
//  Mind Over Marriage
//
//  Created by Matthew Bentley on 7/15/10.
//  Copyright 2010 All rights reserved.
//

#import "AboutViewController.h"


@implementation AboutViewController

@synthesize linkButton;
@synthesize logo;
@synthesize message;

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	
	// Sets up the Splash Screen (what you see at the begining
	splashView *mySplash = [[splashView alloc] initWithImage:[UIImage imageNamed:@"Default.png"]];
	mySplash.animation = SplashViewAnimationFade;
	mySplash.delay = 2;
	
	[mySplash startSplash];
	[mySplash release];
	
    [super viewDidLoad];
}


-(IBAction)hyperLink:(id)sender
{
	NSURL *target = [[NSURL alloc] initWithString:@"http://www.mindovermarriage.com"];
	[[UIApplication sharedApplication] openURL:target];
	[target release];
}


-(IBAction)email:(id)sender
{
	
}

// Adding Email Situation Email
-(IBAction)showPicker:(id)sender
{
	// This sample can run on devices running iPhone OS 2.0 or later  
	// The MFMailComposeViewController class is only available in iPhone OS 3.0 or later. 
	// So, we must verify the existence of the above class and provide a workaround for devices running 
	// earlier versions of the iPhone OS. 
	// We display an email composition interface if MFMailComposeViewController exists and the device can send emails.
	// We launch the Mail application on the device, otherwise.
	
	Class mailClass = (NSClassFromString(@"MFMailComposeViewController"));
	if (mailClass != nil)
	{
		// We must always check whether the current device is configured for sending emails
		if ([mailClass canSendMail])
		{
			[self displayComposerSheet];
		}
		else
		{
			[self launchMailAppOnDevice];
		}
	}
	else
	{
		[self launchMailAppOnDevice];
	}
}

// Adding "Need to Talk" Email
-(IBAction)showPickerNeedToTalk:(id)sender
{
	// This sample can run on devices running iPhone OS 2.0 or later  
	// The MFMailComposeViewController class is only available in iPhone OS 3.0 or later. 
	// So, we must verify the existence of the above class and provide a workaround for devices running 
	// earlier versions of the iPhone OS. 
	// We display an email composition interface if MFMailComposeViewController exists and the device can send emails.
	// We launch the Mail application on the device, otherwise.
	
	Class mailClass = (NSClassFromString(@"MFMailComposeViewController"));
	if (mailClass != nil)
	{
		// We must always check whether the current device is configured for sending emails
		if ([mailClass canSendMail])
		{
			[self displayComposerSheetNeedToTalk];
		}
		else
		{
			[self launchMailAppOnDeviceNeedToTalk];
		}
	}
	else
	{
		[self launchMailAppOnDeviceNeedToTalk];
	}
}


#pragma mark -
#pragma mark Compose Mail

// Displays an email composition interface inside the application. Populates all the Mail fields. 
-(void)displayComposerSheet 
{
	MFMailComposeViewController *picker = [[MFMailComposeViewController alloc] init];
	picker.mailComposeDelegate = self;
	
	[picker setSubject:@"Mind Over Marriage Lesson Request"];
	
	
	// Set up recipients
	NSArray *toRecipients = [NSArray arrayWithObject:@"info@mindovermarriage.com"]; 
		
	[picker setToRecipients:toRecipients];
		
	// Fill out the email body text
	NSString *emailBody = @"In a few sentences, please tell us about the situation you're having trouble with.  As best you can, try to describe actions rather than feelings. For example, instetad of 'My husband never does the dishes, and it makes me angry,' try 'My husband and I are having trouble agreeing whose turn it is to wash the dishes.'";
	[picker setMessageBody:emailBody isHTML:NO];
	
	[self presentModalViewController:picker animated:YES];
    [picker release];
}

-(void)displayComposerSheetNeedToTalk 
{
	MFMailComposeViewController *picker = [[MFMailComposeViewController alloc] init];
	picker.mailComposeDelegate = self;
	
	[picker setSubject:@"Need To Talk?"];
	
	
	// Set up recipients
	NSArray *toRecipients = [NSArray arrayWithObject:@"info@mindovermarriage.com"]; 
	
	[picker setToRecipients:toRecipients];
	
	// Fill out the email body text
	NSString *emailBody = @"If you would like to talk to a Mind Over Marriage expert please give us your name, phone number, and when would be a good time to talk.  The rate for the call is $25 for 20 minutes. We will get back to you as soon as possible to confirm your appointment and give you further instructions.";
	[picker setMessageBody:emailBody isHTML:NO];
	
	[self presentModalViewController:picker animated:YES];
    [picker release];
}



// Dismisses the email composition interface when users tap Cancel or Send. Proceeds to update the message field with the result of the operation.
- (void)mailComposeController:(MFMailComposeViewController*)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error 
{	
	message.hidden = NO;
	// Notifies users about errors associated with the interface
	switch (result)
	{
		case MFMailComposeResultCancelled:
			message.text = @"Result: canceled";
			break;
		case MFMailComposeResultSaved:
			message.text = @"Result: saved";
			break;
		case MFMailComposeResultSent:
			message.text = @"Result: sent";
			break;
		case MFMailComposeResultFailed:
			message.text = @"Result: failed";
			break;
		default:
			message.text = @"Result: not sent";
			break;
	}
	[self dismissModalViewControllerAnimated:YES];
}


#pragma mark -
#pragma mark Workaround

// Launches the Mail application on the device.


-(void)launchMailAppOnDevice
{
	NSString *recipients = @"mailto:info@mindovermarriage.com?&subject=Mind Over Marriage Lesson Request";
	NSString *body = @"&body=In a few sentences, please tell us about the situation you're having trouble with.  As best you can, try to describe actions rather than feelings. For example, instetad of 'My husband never does the dishes, and it makes me angry,' try 'My husband and I are having trouble agreeing whose turn it is to wash the dishes.'";
	
	NSString *email = [NSString stringWithFormat:@"%@%@", recipients, body];
	email = [email stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
	
	[[UIApplication sharedApplication] openURL:[NSURL URLWithString:email]];
}

-(void)launchMailAppOnDeviceNeedToTalk
{
	NSString *recipients = @"mailto:info@mindovermarriage.com?&subject=Need To Talk?";
	NSString *body = @"&body=If you would like to talk to a Mind Over Marriage expert please give us your name, phone number, and when would be a good time to talk.  The rate for the call is $25 for 20 minutes. We will get back to you as soon as possible to confirm your appointment and give you further instructions.";
	
	NSString *email = [NSString stringWithFormat:@"%@%@", recipients, body];
	email = [email stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding];
	
	[[UIApplication sharedApplication] openURL:[NSURL URLWithString:email]];
}

 
 ////////
/*
-(void)willAnimateRotationToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation duration:(NSTimeInterval)duration
{
	if (interfaceOrientation == UIInterfaceOrientationPortrait || interfaceOrientation == UIInterfaceOrientationPortraitUpsideDown)
		{
			linkButton.frame = CGRectMake (8, 353, 304, 38);
			logo.frame = CGRectMake(0, 10, 320, 244);
		}
	else
	{
		linkButton.frame = CGRectMake(85, 105, 304, 38);
		logo.frame = CGRectMake(80, 5, 320, 244);
	}
}
*/

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
	linkButton = nil;
	logo = nil;
}


- (void)dealloc {
	
	[linkButton release];
	[logo release];
    [super dealloc];
}


@end
