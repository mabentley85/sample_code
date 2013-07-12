//
//  SecondLevelViewController.h
//  MOM_Beta4
//
//  Created by Matthew Bentley on 7/15/10.
//  Copyright 2010 All rights reserved.
//

#import <UIKit/UIKit.h>
#import "MOM_Beta4AppDelegate.h"

@interface SecondLevelViewController : UITableViewController <UITableViewDelegate, UITableViewDataSource>
{
	NSArray *scriptList;
	NSString *listTitle;
	NSMutableArray *alphaList2;
}

@property (nonatomic, retain) NSArray *scriptList;
@property (nonatomic, retain) NSString *listTitle;
@property (nonatomic, retain) NSMutableArray *alphaList2;

@end
