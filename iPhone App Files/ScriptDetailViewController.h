//
//  ScriptDetailViewController.h
//  MOM_Beta4
//
//  Created by Matthew Bentley on 7/15/10.
//  Copyright 2010 All rights reserved.
//

#import <UIKit/UIKit.h>

@interface ScriptDetailViewController : UIViewController 
{
	NSString *scriptTitle;
	NSString *scriptFilePath;
	UIWebView *webView;
	NSMutableArray *favList;
	BOOL favorite;
	UIBarButtonItem *editButton;

}

@property (nonatomic, retain) NSString *scriptTitle;
@property (nonatomic, retain) NSString *scriptFilePath;
@property (nonatomic, retain) NSMutableArray *favList;
@property (nonatomic, retain) IBOutlet UIWebView *webView;
@property (nonatomic, retain) UIBarButtonItem *editButton;

- (IBAction)favoriteEdit:(id)sender;

- (BOOL)favoriteListAddButton;

@end
