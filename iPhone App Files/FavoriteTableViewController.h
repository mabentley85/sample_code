//
//  FavoriteTableViewController.h
//  Mind Over Marriage
//
//  Created by Matthew Bentley on 10/31/10.
//  Copyright 2010 All rights reserved.
//

#import <UIKit/UIKit.h>


@interface FavoriteTableViewController : UITableViewController 
<UITableViewDelegate, UITableViewDataSource>
{
	NSMutableArray *list;
}

@property (nonatomic, retain) NSMutableArray *list;

-(IBAction)toggleEdit:(id)sender;

@end
