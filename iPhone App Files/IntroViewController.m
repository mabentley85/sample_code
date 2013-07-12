//
//  IntroViewController.m
//  Mind Over Marriage
//
//  Created by Matthew Bentley on 8/3/10.
//  Copyright 2010 All rights reserved.
//

#import "IntroViewController.h"

@implementation IntroViewController

@synthesize webView;

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
    [super viewDidLoad];
	
	NSString *path = [[NSBundle mainBundle] pathForResource:@"intro" ofType:@"html"];
	NSFileHandle *readHandle = [NSFileHandle fileHandleForReadingAtPath:path];
	NSString *htmlString = [[NSString alloc] initWithData: 
							[readHandle readDataToEndOfFile] encoding: NSUTF8StringEncoding];
	
	webView.opaque = NO;
	webView.backgroundColor = [UIColor clearColor];
	[self.webView loadHTMLString:htmlString baseURL:nil];
	//[htmlString release];	
	
}

// Override Cut/Copy/Paste
-(BOOL)canPerformAction:(SEL)action withSender:(id)sender {
	
	if ( [UIMenuController sharedMenuController] )
	{
		[UIMenuController sharedMenuController].menuVisible = NO;
		
	}
	return NO;
}



- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
	self.webView = nil;
}


- (void)dealloc {
	[webView release];
    [super dealloc];
}


@end
