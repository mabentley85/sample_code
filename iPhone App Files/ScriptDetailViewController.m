//
//  ScriptDetailViewController.m
//  MOM_Beta4
//
//  Created by Matthew Bentley on 7/15/10.
//  Copyright 2010 All rights reserved.
//

#import "ScriptDetailViewController.h"
#import "MOM_Beta4AppDelegate.h"


@implementation ScriptDetailViewController

@synthesize scriptTitle;
@synthesize scriptFilePath;
@synthesize webView;
@synthesize favList;
@synthesize editButton;

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	[super viewDidLoad];
	
	self.title = NSLocalizedString(scriptFilePath, nil);
		
	// Loads HTML file to scriptFilePath
	NSString *path = [[NSBundle mainBundle] pathForResource:scriptFilePath ofType:@"html"];
	NSFileHandle *readHandle = [NSFileHandle fileHandleForReadingAtPath:path];
	NSString *htmlString = [[NSString alloc] initWithData: 
							[readHandle readDataToEndOfFile] encoding: NSUTF8StringEncoding];
	
	// to make html content transparent to its parent view -
	// 1) set the webview's backgroundColor property to [UIColor clearColor]
	// 2) use the content in the html: <body style="background-color: transparent">
	// 3) opaque property set to NO
	//
	webView.opaque = NO;
	webView.backgroundColor = [UIColor clearColor];
	[self.webView loadHTMLString:htmlString baseURL:nil];
	[htmlString release];
	
	// Adding the Favorite Button (only if the particular lesson is not already in the Favorites List)
	
	if ([self favoriteListAddButton] == NO) 
	{
		editButton = [[UIBarButtonItem alloc]
									   initWithBarButtonSystemItem:UIBarButtonSystemItemAdd 
									   target:self 
									   action:@selector(favoriteEdit:)];
		
		self.navigationItem.rightBarButtonItem = editButton;
		//[editButton release];
	}
	
}

- (BOOL)favoriteListAddButton
{
	// Uploads the Favorite Array and assigns to favList
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDirectory = [paths objectAtIndex:0];
	NSString *favListPath = [documentsDirectory stringByAppendingPathComponent:@"editedList.plist"];
	NSMutableArray *array = [[NSMutableArray alloc] 
	initWithContentsOfFile:favListPath];
			
	// Searchs favList Array for currently viewed Lesson
	
	if ([array containsObject:scriptFilePath]) 
	 {
	 return YES;
	 }
	 else {
	 return NO;
	 }
	 	
	[paths release];
	[documentsDirectory release];
	[favListPath release];
	[array release];
}


// Override Cut/Copy/Paste
-(BOOL)canPerformAction:(SEL)action withSender:(id)sender {
	
	if ( [UIMenuController sharedMenuController] )
	{
		[UIMenuController sharedMenuController].menuVisible = NO;
		
	}
	return NO;
}
/////////////////////////

- (IBAction)favoriteEdit:(id)sender
{
	// Adds string object to Favorite List Array, then saves, and reloads the view
	// Plus puts up an alert letting the user know that lesson is in the favorites
	
	// Uploads the Favorite Array and assigns to favList
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDirectory = [paths objectAtIndex:0];
	NSString *favListPath = [documentsDirectory stringByAppendingPathComponent:@"editedList.plist"];
	NSMutableArray *array = [[NSMutableArray alloc] initWithContentsOfFile:favListPath];
	self.favList = [[NSMutableArray alloc] initWithArray:array];
	// Adds Current lesson to Favorites List
	[self.favList addObject:scriptFilePath];
	// Saves changes made
	// Saving the Edit made the the list
	//NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	//NSString *documentsDirectory = [paths objectAtIndex:0];
	NSString *filename = [documentsDirectory stringByAppendingPathComponent:@"editedList.plist"];
	[self.favList writeToFile:filename atomically:YES];
	
	// Notifies User what they Did
	UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"New Favorite" message:@"You just added this lesson to your Favorites List" delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
	[alert show];
	[alert release];	
	
	// Removes the Add button
	self.navigationItem.rightBarButtonItem = nil;
	
	
}




- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;

	self.scriptTitle = nil;
	self.scriptFilePath = nil;
	self.webView = nil;
	[super viewDidUnload];
}


- (void)dealloc {
	
	[editButton release];
	[scriptTitle release];
	[webView release];
	[scriptFilePath release];
    [super dealloc];
}


@end
