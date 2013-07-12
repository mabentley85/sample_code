//
//  ScriptTableViewController.m
//  MOM_Beta4
//
//  Created by Matthew Bentley on 7/15/10.
//  Copyright 2010 All rights reserved.
//

#import "ScriptTableViewController.h"
#import "MOM_Beta4AppDelegate.h"
#import "SecondLevelViewController.h"


@implementation ScriptTableViewController

@synthesize keywordList;
@synthesize alphaList;
@synthesize searchData;
@synthesize searchBar;

#pragma mark -
#pragma mark View lifecycle


- (void)viewDidLoad {
	[super viewDidLoad];

	self.title = @"Keywords";
	
	//  Creating an Array of Dictionaries Through Uploading a pList
	//self.keywordList = [NSMutableArray array];
	NSString *path = [[NSBundle mainBundle] pathForResource:@"keywords" ofType:@"plist"];
	NSMutableArray *array = [[NSMutableArray alloc] initWithContentsOfFile:path];
	//self.keywordList = array;
	
	// Search Modification
	// Alphabetize the Array //
	NSSortDescriptor * alphabetize = [NSSortDescriptor sortDescriptorWithKey:@"" ascending:YES];
	[array sortUsingDescriptors:[NSMutableArray arrayWithObject:alphabetize]];
	self.keywordList = [[NSMutableArray alloc] initWithArray:array];
	
	[array release];
	 
	searching = NO;
	letUserSelectRow = YES;
	 
	// Initialize the Search Array
	self.searchData = [[NSMutableArray alloc] init];
	//////// 
	
	UIBarButtonItem *backButton1 = [[UIBarButtonItem alloc] initWithTitle:@"Back" 
																	style:UIBarButtonItemStyleBordered 
																   target:nil 
																   action:nil];
    self.navigationItem.backBarButtonItem = backButton1;
	[backButton1 release];
}

#pragma mark -
#pragma mark Search Methods

- (void) searchBarTextDidBeginEditing:(UISearchBar *)theSearchBar {
	
	//This method is called again when the user clicks back from the detail view.
	//So the overlay is displayed on the results, which is something we do not want to happen.
	if(searching)
		return;
	
	searching = YES;
	letUserSelectRow = NO;
	self.tableView.scrollEnabled = NO;
	
	//Add the done button.
	self.navigationItem.rightBarButtonItem = [[[UIBarButtonItem alloc] 
											   initWithBarButtonSystemItem:UIBarButtonSystemItemDone
											   target:self action:@selector(doneSearching_Clicked:)] autorelease];
	
}

- (void)searchBar:(UISearchBar *)theSearchBar textDidChange:(NSString *)searchText {
	
	//Remove all objects first.
	[searchData removeAllObjects];
	
	if([searchText length] > 0) 
	{
		searching = YES;
		letUserSelectRow = YES;
		self.tableView.scrollEnabled = YES;
		[self searchTableView];
	}
	
	else {
		
		searching = NO;
		letUserSelectRow = NO;
		self.tableView.scrollEnabled = NO;
	}
	
	[self.tableView reloadData];
	
}

- (void) searchTableView {
	
	NSString *searchText = searchBar.text;
	NSMutableArray *searchArray = [[NSMutableArray alloc] init];

	[searchArray addObjectsFromArray:self.keywordList];
		
	for (NSString *sTemp in searchArray)
	{
		NSRange titleResultsRange = [sTemp rangeOfString:searchText options:NSCaseInsensitiveSearch];
		
		if (titleResultsRange.length > 0)
		{
			[self.searchData addObject:sTemp];
		}
	}
	
	[searchArray release];
	searchArray = nil;

}

#pragma mark -
#pragma mark Search Bar Methods

- (void) doneSearching_Clicked:(id)sender {
	
	searchBar.text = @"";
	[searchBar resignFirstResponder];
	
	letUserSelectRow = YES;
	searching = NO;
	self.navigationItem.rightBarButtonItem = nil;
	self.tableView.scrollEnabled = YES;
	
	[self.tableView reloadData];
}

- (void) searchBarSearchButtonClicked:(UISearchBar *)theSearchBar {
	
	[self searchTableView];
}

#pragma mark -
#pragma mark Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections.
    return 1;
}


- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
   
	if (searching) {
		return [searchData count];
	}
	
	else {
		return [keywordList count];
	}
}


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
    }
    
    // Configure the cell...
	if (searching) {
		cell.textLabel.text = NSLocalizedString([searchData objectAtIndex:indexPath.row], nil);  // Complete List
	}
	else {
		cell.textLabel.text = NSLocalizedString([keywordList objectAtIndex:indexPath.row], nil);  // Search List
	}
	cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
    
    return cell;
}

#pragma mark -
#pragma mark Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    // Navigation logic may go here. Create and push another view controller.
	
	NSString *selectedKeyword = nil;
	
	if(searching)
	{
		selectedKeyword = [searchData objectAtIndex:indexPath.row];
	}
	else {
		selectedKeyword = [keywordList objectAtIndex:indexPath.row];
	}
	
	
	SecondLevelViewController *secondLevelViewController = [[SecondLevelViewController alloc] 
															initWithNibName:@"SecondTableView" bundle:nil];
		
	// Pass the selected object to the new view controller.
	secondLevelViewController.listTitle = selectedKeyword;
	[self.navigationController pushViewController:secondLevelViewController animated:YES];
	secondLevelViewController = nil;
	[secondLevelViewController release];
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
	
	self.searchBar = nil;
	self.searchData = nil;	
	self.keywordList = nil;
	self.alphaList = nil;
}


- (void)dealloc {
	
	[searchBar release];
	[searchData release];
	[alphaList release];
	[keywordList release];
    [super dealloc];
}


@end

