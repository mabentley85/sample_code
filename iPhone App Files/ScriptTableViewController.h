//
//  ScriptTableViewController.h
//  MOM_Beta4
//
//  Created by Matthew Bentley on 7/15/10.
//  Copyright 2010 All rights reserved.
//

#import <UIKit/UIKit.h>


@interface ScriptTableViewController : UITableViewController 
<UITableViewDelegate, UITableViewDataSource, UISearchBarDelegate> 
{
	
	NSMutableArray *keywordList;
	NSMutableArray *alphaList;
	// Search Additions
	NSMutableArray *searchData;
	IBOutlet UISearchBar *searchBar;
	BOOL searching;
	BOOL letUserSelectRow;
}

@property (nonatomic, retain) NSMutableArray *keywordList;
@property (nonatomic, retain) NSMutableArray *alphaList;

// Search additions
@property (nonatomic, retain) NSMutableArray *searchData;
@property (nonatomic, retain) IBOutlet UISearchBar *searchBar;

- (void) searchTableView;
- (void) doneSearching_Clicked:(id)sender;


@end
