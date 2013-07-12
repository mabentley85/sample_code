//
//  SecondLevelViewController.m
//  MOM_Beta4
//
//  Created by Matthew Bentley on 7/15/10.
//  Copyright 2010 All rights reserved.
//

#import "SecondLevelViewController.h"
#import "ScriptDetailViewController.h"

@implementation SecondLevelViewController

@synthesize scriptList;
@synthesize listTitle;
@synthesize alphaList2;


#pragma mark -
#pragma mark View lifecycle


- (void)viewDidLoad {
    [super viewDidLoad];
 
	NSString *tempTitle = NSLocalizedString(self.listTitle, nil);
 	NSString *titleString = [[NSString alloc] initWithFormat:@"%@", tempTitle];
	self.title = NSLocalizedString(titleString, nil);
 	[titleString release];
 
	// Old Way ver 1
	//// Loading the Test Array to make sure the Values from the Dictionaries in the Root Controller got here OK.
	//NSMutableArray *array = [[NSMutableArray alloc] initWithArray:scriptList]; 	
	
	// Loading New Array From File (ver 2.0)
	NSString *path = [[NSBundle mainBundle] pathForResource:listTitle ofType:@"plist"];
	NSMutableArray *array = [[NSMutableArray alloc] initWithContentsOfFile:path];
	self.scriptList = array;
	
	
	// Alphabetize the Array //
	NSSortDescriptor * alphabetize = [NSSortDescriptor sortDescriptorWithKey:@"" ascending:YES];
	[array sortUsingDescriptors:[NSArray arrayWithObject:alphabetize]];
	
	 self.alphaList2 = array;
	////////////////////////////
	
	
	
	
	[array release];
	
	UIBarButtonItem *backButton1 = [[UIBarButtonItem alloc] initWithTitle:@"Back" 
																	style:UIBarButtonItemStyleBordered 
																   target:nil 
																   action:nil];
    self.navigationItem.backBarButtonItem = backButton1;
	[backButton1 release];

    // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
    // self.navigationItem.rightBarButtonItem = self.editButtonItem;
}


/*
- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
}
*/

- (void)viewDidAppear:(BOOL)animated {
    // Bringing Back the TabBar Controller //
	MOM_Beta4AppDelegate *delegate = (MOM_Beta4AppDelegate *)[[UIApplication sharedApplication] delegate];
	UIView *tabBar = [delegate.rootController.view.subviews objectAtIndex:1];
	tabBar.hidden = FALSE;	
	[super viewDidAppear:animated];
}

#pragma mark -
#pragma mark Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections.
    return 1;
}


- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
    return [alphaList2 count];
}


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
    }
    
    // Configure the cell...
	
	NSUInteger row = [indexPath row];
	cell.textLabel.text = NSLocalizedString([self.alphaList2 objectAtIndex: row], nil);
	cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;

    
    return cell;
}

#pragma mark -
#pragma mark Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    // Navigation logic may go here. Create and push another view controller.
	
	ScriptDetailViewController *scriptDetailViewController = [[ScriptDetailViewController alloc] initWithNibName:@"ScriptDetailView" bundle:nil];
	// ...
	NSUInteger row = [indexPath row];
	//scriptDetailViewController.scriptTitle = [[self.scriptList objectAtIndex: row] objectForKey:@"scriptTitle"];
	scriptDetailViewController.scriptFilePath = [self.scriptList objectAtIndex: row];
	
	
	// Pass the selected object to the new view controller.
	[self.navigationController pushViewController:scriptDetailViewController animated:YES];
	[scriptDetailViewController release];
}


#pragma mark -
#pragma mark Memory management

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Relinquish ownership any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    // Relinquish ownership of anything that can be recreated in viewDidLoad or on demand.
    // For example: self.myOutlet = nil;
	
	self.scriptList = nil;
	self.listTitle = nil;
}


- (void)dealloc {
	[listTitle release];
	[scriptList release];
    [super dealloc];
}


@end

