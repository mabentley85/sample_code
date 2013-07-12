//
//  FavoriteTableViewController.m
//  Mind Over Marriage
//
//  Created by Matthew Bentley on 10/31/10.
//  Copyright 2010 All rights reserved.
//

#import "FavoriteTableViewController.h"
#import "ScriptDetailViewController.h"


@implementation FavoriteTableViewController

@synthesize list;

#pragma mark -
#pragma mark View Lifecycle


- (void)viewDidLoad {
    [super viewDidLoad];
	
	self.title = @"Favorite Lessons";
	
	// Favorites List
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDirectory = [paths objectAtIndex:0];
	NSString *path = [documentsDirectory stringByAppendingPathComponent:@"editedList.plist"];
	NSMutableArray *array = [[NSMutableArray alloc] 
							 initWithContentsOfFile:path];
	self.list = array;
	
	[array release];
	
	// Adding the Edit Button
	UIBarButtonItem *editButton = [[UIBarButtonItem alloc]
                                   initWithTitle:@"Delete"
                                   style:UIBarButtonItemStyleBordered
                                   target:self
                                   action:@selector(toggleEdit:)];
    self.navigationItem.rightBarButtonItem = editButton;
    [editButton release];
	
	UIBarButtonItem *backButton1 = [[UIBarButtonItem alloc] initWithTitle:@"Back" 
																	style:UIBarButtonItemStyleBordered 
																   target:nil 
																   action:nil];
    self.navigationItem.backBarButtonItem = backButton1;
	[backButton1 release];
}

- (void)viewWillAppear:(BOOL)animated {
	
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDirectory = [paths objectAtIndex:0];
	NSString *path = [documentsDirectory stringByAppendingPathComponent:@"editedList.plist"];
	NSMutableArray *array = [[NSMutableArray alloc] 
							 initWithContentsOfFile:path];
	self.list = array;
	
	[array release];
	[self.tableView reloadData];

}
/*
// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}
*/


#pragma mark -
#pragma mark Table View Data Source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections.
    return 1;
}


- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
    return [list count];
}


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
    }
    
    // Configure the cell...
	NSInteger row = [indexPath row];
    cell.textLabel.text = NSLocalizedString([self.list objectAtIndex:row], nil);
    
    return cell;
}


/*
// Override to support conditional editing of the table view.
- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath {
    // Return NO if you do not want the specified item to be editable.
    return YES;
}
*/



// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath {
    
 NSUInteger row = [indexPath row];
 [self.list removeObjectAtIndex:row];
 [tableView deleteRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] 
 withRowAnimation:UITableViewRowAnimationFade];
 
 // Saving the Edit made the the list
 NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
 NSString *documentsDirectory = [paths objectAtIndex:0];
 NSString *filename = [documentsDirectory stringByAppendingPathComponent:@"editedList.plist"];
 [self.list writeToFile:filename atomically:YES]; 
}

#pragma mark -
#pragma mark Table View Delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    /// Navigation logic may go here. Create and push another view controller.
	
	ScriptDetailViewController *scriptDetailViewController = [[ScriptDetailViewController alloc] initWithNibName:@"ScriptDetailView" bundle:nil];
	// ...
	NSUInteger row = [indexPath row];
	//scriptDetailViewController.scriptTitle = [[self.scriptList objectAtIndex: row] objectForKey:@"scriptTitle"];
	scriptDetailViewController.scriptFilePath = [self.list objectAtIndex: row];
	
	// Pass the selected object to the new view controller.
	[self.navigationController pushViewController:scriptDetailViewController animated:YES];
	[scriptDetailViewController release];
}

#pragma mark -
#pragma mark Custom Methods

-(IBAction)toggleEdit:(id)sender {
    [self.tableView setEditing:!self.tableView.editing animated:YES];
    
    if (self.tableView.editing)
        [self.navigationItem.rightBarButtonItem setTitle:@"Done"];
    else
        [self.navigationItem.rightBarButtonItem setTitle:@"Delete"];
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
}


- (void)dealloc {
    [super dealloc];
}

@end

