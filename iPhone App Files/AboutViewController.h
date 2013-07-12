//
//  AboutViewController.h
//  MOM_Beta4
//
//  Created by Matthew Bentley on 7/15/10.
//  Copyright 2010 All rights reserved.
//

#import <UIKit/UIKit.h>
#import "splashView.h"
#import <MessageUI/MessageUI.h>
#import <MessageUI/MFMailComposeViewController.h>


@interface AboutViewController : UIViewController <splashViewDelegate, MFMailComposeViewControllerDelegate> {
	
	UIButton *linkButton;
	UIImageView *logo;
	IBOutlet UILabel *message;
}

@property (nonatomic, retain) IBOutlet UILabel *message;
@property (nonatomic, retain) IBOutlet UIButton *linkButton;
@property (nonatomic, retain) IBOutlet UIImageView *logo;

-(IBAction)hyperLink:(id)sender;

-(IBAction)email:(id)sender;

// Adding Email
-(IBAction)showPicker:(id)sender;
-(IBAction)showPickerNeedToTalk:(id)sender;
-(void)displayComposerSheet;
-(void)displayComposerSheetNeedToTalk;
-(void)launchMailAppOnDevice;
-(void)launchMailAppOnDeviceNeedToTalk;

@end
